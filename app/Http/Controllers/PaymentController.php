<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Payment;
use App\Models\BookPurchase;
use App\Services\YengaPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private const SUCCESS = ['DONE', 'SUCCESS', 'SUCCESSFUL', 'PAID', 'COMPLETED', 'APPROVED', 'OK'];
    private const PENDING = ['PENDING', 'INIT', 'INITIATED', 'CREATED', 'PROCESSING', 'IN_PROGRESS', 'WAITING'];
    private const FAILED  = ['FAILED', 'CANCELLED', 'CANCELED', 'EXPIRED', 'REFUSED', 'REJECTED'];

    public function createBookPayment(Request $request, YengaPayService $yenga)
{
    $user = $request->user();

    $validated = $request->validate([
        'book_id' => ['required', 'integer', 'exists:books,id'],
    ]);

    $book = Book::findOrFail($validated['book_id']);

    if ($book->access_type !== 'paid') {
        return response()->json(['message' => "Ce livre n'est pas payant."], 422);
    }

    // Déjà acheté ?
    $alreadyBought = BookPurchase::where('user_id', $user->id)
        ->where('book_id', $book->id)
        ->whereNotNull('purchased_at')
        ->exists();

    if ($alreadyBought) {
        return response()->json(['message' => 'Livre déjà acheté.'], 200);
    }

    $purchase = BookPurchase::firstOrCreate(
        ['user_id' => $user->id, 'book_id' => $book->id],
        ['price' => (float) $book->price, 'currency' => 'XOF']
    );

    // Paiement pending existant ?
    $existing = Payment::where('user_id', $user->id)
        ->where('payable_type', BookPurchase::class)
        ->where('payable_id', $purchase->id)
        ->where('status', 'PENDING')
        ->latest('id')
        ->first();

    if ($existing && $existing->checkout_url) {
        return response()->json([
            'checkout_url' => $existing->checkout_url,
            'reference'    => $existing->reference,
            'payment_id'   => $existing->id,
        ], 200);
    }

    $reference = 'BOOK-' . $book->id . '-' . $user->id . '-' . Str::upper(Str::random(10));

    // ✅ redirectUrl propre
    $redirectUrl = route('payment.return', ['reference' => $reference]);

    // ✅ IMPORTANT : Pour réduire les 500 YengaPay, commence sans pictures si tu veux tester
    $pictures = [];
    if ($book->cover) {
        $coverUrl = asset('storage/' . $book->cover);
        // garder seulement https
        if (str_starts_with($coverUrl, 'https://')) $pictures = [$coverUrl];
    }

    $payload = [
        'paymentAmount' => (int) $book->price,
        'reference'     => $reference,
        'redirectUrl'   => $redirectUrl,
        'articles' => [[
            'title'       => (string) $book->title,
            'description' => $book->description ? Str::limit($book->description, 180) : 'Achat de livre',
            'pictures'    => $pictures,
            'price'       => (int) $book->price,
        ]],
    ];

    try {
        $data = $yenga->createPaymentIntent($payload);
    } catch (\Throwable $e) {
        // ✅ Ne renvoie plus 500 "muet" côté client
        return response()->json([
            'message' => "YengaPay: impossible de créer l'intention de paiement.",
            'error'   => $e->getMessage(),
        ], 502);
    }

    $intentId = $data['paymentIntentId'] ?? $data['id'] ?? null;
    $checkout = $data['checkoutPageUrlWithPaymentToken'] ?? $data['checkout_url'] ?? null;

    // mapping status
    $statusRaw = $data['paymentStatus'] ?? $data['transactionStatus'] ?? $data['status'] ?? 'PENDING';
    $status = strtoupper((string) $statusRaw);
    $mapped = 'PENDING';
    if (in_array($status, self::SUCCESS, true)) $mapped = 'SUCCESS';
    elseif (in_array($status, self::FAILED, true)) $mapped = 'FAILED';

    $payment = Payment::create([
        'user_id'            => $user->id,
        'payable_type'       => BookPurchase::class,
        'payable_id'         => $purchase->id,
        'provider'           => 'yengapay',
        'provider_intent_id' => $intentId,
        'reference'          => $reference,
        'provider_project_id'=> (string) ($data['projectId'] ?? config('services.yengapay.project_id')),
        'provider_group_id'  => (string) config('services.yengapay.organization_id'),

        // ✅ client paie les frais -> amount reste prix produit
        'amount'             => (float) $book->price,
        'fees'               => (float) ($data['paymentFees'] ?? 0),
        'currency'           => (string) ($data['currency'] ?? 'XOF'),

        'status'             => $mapped,
        'token'              => $data['token'] ?? null,
        'checkout_url'       => $checkout,
        'provider_payload'   => $data,
    ]);

    $purchase->payment_id = $payment->id;
    $purchase->save();

    return response()->json([
        'checkout_url' => $payment->checkout_url,
        'reference'    => $payment->reference,
        'payment_id'   => $payment->id,
    ], 200);
}


    public function return(Request $request, YengaPayService $yenga)
    {
        $reference = (string) $request->query('reference', '');
        $intentId  = (string) $request->query('paymentIntentId', '');

        $payment = null;

        if ($reference !== '') {
            $payment = Payment::where('reference', $reference)->latest('id')->first();
        }
        if (!$payment && $intentId !== '') {
            $payment = Payment::where('provider_intent_id', $intentId)->latest('id')->first();
        }

        if (!$payment) {
            return view('front.pages.payment_return', [
                'message' => 'Paiement reçu. Vérification en cours…'
            ]);
        }

        // fallback: vérif distante (si webhook en retard)
        try {
            if ($payment->provider_intent_id) {
                $remote = $yenga->getPaymentIntent($payment->provider_intent_id);

                $statusRemoteRaw =
                    data_get($remote, 'paymentStatus')
                    ?? data_get($remote, 'transactionStatus')
                    ?? data_get($remote, 'status')
                    ?? '';

                $statusRemote = strtoupper((string) $statusRemoteRaw);

                $mapped = 'PENDING';
                if (in_array($statusRemote, self::SUCCESS, true)) $mapped = 'SUCCESS';
                elseif (in_array($statusRemote, self::FAILED, true)) $mapped = 'FAILED';

                DB::transaction(function () use ($payment, $remote, $mapped) {
                    $p = Payment::lockForUpdate()->find($payment->id);
                    if (!$p) return;

                    $p->status = $mapped;
                    $p->provider_payload = $remote;

                    // fees update (optionnel)
                    $p->fees = (float) (data_get($remote, 'paymentFees') ?? $p->fees ?? 0);

                    if ($mapped === 'SUCCESS' && !$p->paid_at) $p->paid_at = now();
                    $p->save();

                    if ($mapped === 'SUCCESS' && !$p->is_used && $p->payable_type === BookPurchase::class) {
                        $purchase = BookPurchase::lockForUpdate()->find($p->payable_id);
                        if ($purchase && !$purchase->purchased_at) {
                            $purchase->purchased_at = now();
                            $purchase->payment_id = $p->id;
                            $purchase->save();
                        }
                        $p->is_used = true;
                        $p->save();
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // redirection finale
        if ($payment->payable_type === BookPurchase::class) {
            $purchase = BookPurchase::find($payment->payable_id);
            if ($purchase) {
                $book = Book::find($purchase->book_id);
                if ($book) {
                    return redirect()
                        ->route('books.show', $book->slug)
                        ->with('success', 'Paiement en cours de validation…');
                }
            }
        }

        return view('front.pages.payment_return', [
            'message' => 'Paiement en cours de confirmation…'
        ]);
    }
}
