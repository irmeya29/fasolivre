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

        $alreadyBought = BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNotNull('purchased_at')
            ->exists();

        if ($alreadyBought) {
            return response()->json(['message' => 'Livre déjà acheté.'], 200);
        }

        $purchase = BookPurchase::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['price' => (float)$book->price, 'currency' => 'XOF']
        );

        // idempotence: reuse pending payment
        $existing = Payment::where('user_id', $user->id)
            ->where('payable_type', BookPurchase::class)
            ->where('payable_id', $purchase->id)
            ->whereIn('status', ['PENDING'])
            ->orderByDesc('id')
            ->first();

        if ($existing && $existing->checkout_url) {
            return response()->json([
                'checkout_url' => $existing->checkout_url,
                'reference' => $existing->reference,
                'payment_id' => $existing->id,
            ]);
        }

        $reference = 'BOOK-' . $book->id . '-' . $user->id . '-' . Str::upper(Str::random(10));

        $payload = [
            'paymentAmount' => (int) $book->price,
            'reference' => $reference,
            'articles' => [[
                'title' => $book->title,
                'description' => $book->description ? Str::limit($book->description, 180) : 'Achat de livre',
                'pictures' => $book->cover ? [asset('storage/' . $book->cover)] : [],
                'price' => (int) $book->price,
            ]],
        ];

        $data = $yenga->createPaymentIntent($payload);

        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_type' => BookPurchase::class,
            'payable_id' => $purchase->id,

            'provider' => 'yengapay',
            'provider_intent_id' => $data['paymentIntentId'] ?? ($data['id'] ?? null),
            'reference' => $reference,

            'provider_project_id' => (string)($data['projectId'] ?? config('services.yengapay.project_id')),
            'provider_group_id' => (string) config('services.yengapay.organization_id'),

            'amount' => (float)($book->price),
            'fees' => (float)($data['paymentFees'] ?? 0),
            'currency' => (string)($data['currency'] ?? 'XOF'),

            'status' => (string)($data['paymentStatus'] ?? ($data['transactionStatus'] ?? 'PENDING')),
            'token' => $data['token'] ?? null,
            'checkout_url' => $data['checkoutPageUrlWithPaymentToken'] ?? ($data['checkout_url'] ?? null),
            'provider_payload' => $data,
        ]);

        $purchase->payment_id = $payment->id;
        $purchase->save();

        return response()->json([
            'checkout_url' => $payment->checkout_url,
            'reference' => $payment->reference,
            'payment_id' => $payment->id,
        ]);
    }

    public function return(Request $request, YengaPayService $yenga)
    {
        // Optionnel: si YengaPay renvoie reference/intentId en query
        $reference = (string) $request->query('reference', '');
        $intentId  = (string) $request->query('paymentIntentId', '');

        $payment = null;

        if ($reference !== '') {
            $payment = Payment::where('reference', $reference)->latest()->first();
        }

        if (!$payment && $intentId !== '') {
            $payment = Payment::where('provider_intent_id', $intentId)->latest()->first();
        }

        if (!$payment) {
            return view('front.pages.payment_return', [
                'message' => 'Paiement reçu. Vérification en cours…'
            ]);
        }

        // fallback: check intent status
        try {
            if ($payment->provider_intent_id) {
                $remote = $yenga->getPaymentIntent($payment->provider_intent_id);

                $status = strtoupper((string)(
                    data_get($remote, 'paymentStatus')
                    ?? data_get($remote, 'transactionStatus')
                    ?? data_get($remote, 'status')
                    ?? ''
                ));

                if (in_array($status, ['DONE','SUCCESS','PAID','COMPLETED','APPROVED'], true)) {
                    DB::transaction(function() use ($payment, $remote, $status) {
                        $payment->status = $status;
                        $payment->provider_payload = $remote;
                        $payment->paid_at = $payment->paid_at ?? now();
                        $payment->save();

                        if (!$payment->is_used && $payment->payable_type === BookPurchase::class) {
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
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // redirige vers le livre si achat
        if ($payment->payable_type === BookPurchase::class) {
            $purchase = BookPurchase::find($payment->payable_id);
            if ($purchase) {
                $book = Book::find($purchase->book_id);
                if ($book) {
                    return redirect()->route('books.show', $book->slug);
                }
            }
        }

        return view('front.pages.payment_return', [
            'message' => 'Paiement en cours de confirmation…'
        ]);
    }
}
