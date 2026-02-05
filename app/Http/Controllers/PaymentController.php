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
    // Statuts possibles côté YengaPay (à harmoniser)
    private const SUCCESS = ['DONE', 'SUCCESS', 'SUCCESSFUL', 'PAID', 'COMPLETED', 'APPROVED', 'OK'];
    private const PENDING = ['PENDING', 'INIT', 'INITIATED', 'CREATED', 'PROCESSING', 'IN_PROGRESS', 'WAITING'];
    private const FAILED  = ['FAILED', 'CANCELLED', 'CANCELED', 'EXPIRED', 'REFUSED', 'REJECTED'];

    /**
     * Crée un paiement pour un livre payant et renvoie checkout_url.
     */
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

        // Achat local (unique user+book)
        $purchase = BookPurchase::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['price' => (float) $book->price, 'currency' => 'XOF']
        );

        // Paiement pending existant ?
        $existing = Payment::where('user_id', $user->id)
            ->where('payable_type', BookPurchase::class)
            ->where('payable_id', $purchase->id)
            ->whereIn('status', self::PENDING)
            ->latest('id')
            ->first();

        if ($existing && $existing->checkout_url) {
            return response()->json([
                'checkout_url' => $existing->checkout_url,
                'reference'    => $existing->reference,
                'payment_id'   => $existing->id,
            ]);
        }

        // Référence unique
        $reference = 'BOOK-' . $book->id . '-' . $user->id . '-' . Str::upper(Str::random(10));

        // ✅ IMPORTANT: mettre une redirectUrl avec reference => return() pourra retrouver le paiement
        $redirectUrl = route('payment.return', ['reference' => $reference], true);

        // Payload YengaPay
        $payload = [
            'paymentAmount' => (int) $book->price,
            'currency'      => 'XOF',
            'reference'     => $reference,
            'redirectUrl'   => $redirectUrl, // ✅ clé souvent attendue côté YengaPay
            'articles' => [[
                'title'       => $book->title,
                'description' => $book->description ? Str::limit($book->description, 180) : 'Achat de livre',
                'pictures'    => $book->cover ? [asset('storage/' . $book->cover)] : [],
                'price'       => (int) $book->price,
            ]],
        ];

        $data = $yenga->createPaymentIntent($payload);

        // Extraction champs robustes
        $intentId = $data['paymentIntentId'] ?? $data['id'] ?? null;
        $checkout = $data['checkoutPageUrlWithPaymentToken'] ?? $data['checkout_url'] ?? null;

        $statusRaw = $data['paymentStatus'] ?? $data['transactionStatus'] ?? $data['status'] ?? 'PENDING';
        $status = strtoupper((string) $statusRaw);
        if (in_array($status, self::SUCCESS, true)) $status = 'SUCCESS';
        elseif (in_array($status, self::FAILED, true)) $status = 'FAILED';
        elseif (!in_array($status, self::PENDING, true)) $status = 'PENDING';

        $payment = Payment::create([
            'user_id'            => $user->id,
            'payable_type'       => BookPurchase::class,
            'payable_id'         => $purchase->id,
            'provider'           => 'yengapay',
            'provider_intent_id' => $intentId,
            'reference'          => $reference,
            'provider_project_id'=> (string) ($data['projectId'] ?? config('services.yengapay.project_id')),
            'provider_group_id'  => (string) config('services.yengapay.organization_id'),

            'amount'             => (float) $book->price,
            'fees'               => (float) ($data['paymentFees'] ?? 0),
            'currency'           => (string) ($data['currency'] ?? 'XOF'),

            'status'             => $status,
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
        ]);
    }

    /**
     * Retour utilisateur après paiement.
     * Doit rediriger vers le livre, et activer l'achat si paiement OK.
     */
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

        // Si on ne retrouve pas, page simple
        if (!$payment) {
            return view('front.pages.payment_return', [
                'message' => 'Paiement reçu. Vérification en cours…'
            ]);
        }

        // ✅ On tente de vérifier le statut distant (utile si webhook retardé)
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

                // Update local
                DB::transaction(function () use ($payment, $remote, $mapped) {
                    $payment = Payment::lockForUpdate()->find($payment->id);
                    if (!$payment) return;

                    $payment->status = $mapped;
                    $payment->provider_payload = $remote;

                    if ($mapped === 'SUCCESS' && !$payment->paid_at) {
                        $payment->paid_at = now();
                    }
                    $payment->save();

                    // ✅ Activer l'achat (idempotent)
                    if ($mapped === 'SUCCESS' && !$payment->is_used && $payment->payable_type === BookPurchase::class) {
                        $purchase = BookPurchase::lockForUpdate()->find($payment->payable_id);
                        if ($purchase && !$purchase->purchased_at) {
                            $purchase->purchased_at = now();
                            $purchase->payment_id = $payment->id;
                            $purchase->save();
                        }
                        $payment->is_used = true;
                        $payment->save();
                    }
                });
            }
        } catch (\Throwable $e) {
            // on ignore, on ne bloque pas l’utilisateur
        }

        // ✅ Redirection finale vers le livre
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
