<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Payment;
use App\Models\BookPurchase;
use App\Services\YengaPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Client\RequestException;

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

        // Référence unique
        $reference = 'BOOK-' . $book->id . '-' . $user->id . '-' . Str::upper(Str::random(10));
        $redirectUrl = route('payment.return', ['reference' => $reference]);

        // pictures: uniquement https (sinon on n'envoie rien)
        $pictures = [];
        if (!empty($book->cover)) {
            $coverUrl = asset('storage/' . $book->cover);
            if (is_string($coverUrl) && str_starts_with($coverUrl, 'https://')) {
                $pictures = [$coverUrl];
            }
        }

        // ✅ Payload "riche" (peut parfois faire bugger YengaPay)
        $payloadRich = [
            'paymentAmount' => (int) $book->price,
            'reference'     => $reference,
            'redirectUrl'   => $redirectUrl,
            'articles' => [[
                'title'       => (string) $book->title,
                'description' => $book->description ? Str::limit($book->description, 180) : 'Achat de livre',
                'pictures'    => $pictures, // peut être []
                'price'       => (int) $book->price,
            ]],
        ];

        // ✅ Payload minimal (ton CURL)
        $payloadMin = [
            'paymentAmount' => (int) $book->price,
            'reference'     => $reference,
            'articles' => [[
                'title'       => (string) $book->title,
                'description' => 'Achat de livre',
                'price'       => (int) $book->price,
            ]],
        ];

        // 1) Essai riche -> 2) si erreur, retry minimal
        try {
            $data = $yenga->createPaymentIntent($payloadRich);
        } catch (RequestException $e) {
            $status = $e->response?->status();
            $body   = $e->response?->json() ?? $e->response?->body();

            Log::warning('YengaPay rich payload failed, retrying minimal', [
                'status' => $status,
                'body'   => $body,
                'ref'    => $reference,
            ]);

            try {
                $data = $yenga->createPaymentIntent($payloadMin);
            } catch (RequestException $e2) {
                $status2 = $e2->response?->status();
                $body2   = $e2->response?->json() ?? $e2->response?->body();

                return response()->json([
                    'message' => "YengaPay: impossible de créer l'intention de paiement.",
                    'status'  => $status2,
                    'body'    => $body2,
                ], 502);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'message' => "YengaPay: impossible de créer l'intention de paiement.",
                'error'   => $e->getMessage(),
            ], 502);
        }

        // Extraction champs robustes
        $intentId = $data['paymentIntentId'] ?? $data['id'] ?? null;
        $checkout = $data['checkoutPageUrlWithPaymentToken'] ?? $data['checkout_url'] ?? null;

        // Status mapping
        $statusRaw = $data['paymentStatus'] ?? $data['transactionStatus'] ?? $data['status'] ?? 'PENDING';
        $status = strtoupper((string) $statusRaw);

        $mapped = 'PENDING';
        if (in_array($status, self::SUCCESS, true)) $mapped = 'SUCCESS';
        elseif (in_array($status, self::FAILED, true)) $mapped = 'FAILED';

        // Create payment local
        $payment = Payment::create([
            'user_id'            => $user->id,
            'payable_type'       => BookPurchase::class,
            'payable_id'         => $purchase->id,
            'provider'           => 'yengapay',
            'provider_intent_id' => $intentId,
            'reference'          => $reference,
            'provider_project_id'=> (string) ($data['projectId'] ?? config('services.yengapay.project_id')),
            'provider_group_id'  => (string) config('services.yengapay.organization_id'),

            // ✅ client paie les frais => amount = prix du livre, fees = paymentFees
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

    /**
     * Retour utilisateur après paiement.
     * Essaie de vérifier l'intention côté YengaPay (utile si webhook en retard).
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

        if (!$payment) {
            return view('front.pages.payment_return', [
                'message' => 'Paiement reçu. Vérification en cours…'
            ]);
        }

        // Vérif distante
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
                    $payment = Payment::lockForUpdate()->find($payment->id);
                    if (!$payment) return;

                    $payment->status = $mapped;
                    $payment->provider_payload = $remote;

                    // ✅ client paie les frais
                    $payment->fees = (float) (data_get($remote, 'paymentFees') ?? $payment->fees ?? 0);

                    if ($mapped === 'SUCCESS' && !$payment->paid_at) {
                        $payment->paid_at = now();
                    }
                    $payment->save();

                    // Activer achat (idempotent)
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
            // ne bloque pas l'utilisateur
            Log::warning('Payment return remote check failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        return view('front.pages.payment_return', [
            'message' => 'Paiement en cours de confirmation…'
        ]);
    }
}
