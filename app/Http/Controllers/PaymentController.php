<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Payment;
use App\Models\BookPurchase;
use App\Services\YengaPayService;
use Illuminate\Http\Request;
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

        // Sécurité: on paie seulement les livres payants
        if ($book->access_type !== 'paid') {
            return response()->json([
                'message' => "Ce livre n'est pas payant."
            ], 422);
        }

        // Si déjà acheté -> on ne recrée pas de paiement
        $alreadyBought = BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNotNull('purchased_at')
            ->exists();

        if ($alreadyBought) {
            return response()->json([
                'message' => 'Livre déjà acheté.',
            ], 200);
        }

        // Crée (ou récupère) l'achat local
        $purchase = BookPurchase::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['price' => (float)$book->price, 'currency' => 'XOF']
        );

        // ✅ Idempotence: s'il y a déjà un paiement PENDING pour ce purchase, on le renvoie
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

        // Référence unique
        $reference = 'BOOK-' . $book->id . '-' . $user->id . '-' . Str::upper(Str::random(10));

        // Payload YengaPay
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
            'provider_intent_id' => $data['id'] ?? null,
            'reference' => $reference,

            'provider_project_id' => (string)($data['projectId'] ?? config('services.yengapay.project_id')),
            'provider_group_id' => (string) config('services.yengapay.organization_id'),

            // Montant de base = prix du livre (les frais sont stockés séparément)
            'amount' => (float)($book->price),
            'fees' => (float)($data['paymentFees'] ?? 0),
            'currency' => (string)($data['currency'] ?? 'XOF'),

            'status' => (string)($data['transactionStatus'] ?? 'PENDING'),
            'token' => $data['token'] ?? null,
            'checkout_url' => $data['checkoutPageUrlWithPaymentToken'] ?? null,
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

    /**
     * Route de retour après paiement (si activée côté YengaPay).
     * Ici on affiche juste une page simple ou on renvoie un JSON.
     */
    public function return(Request $request)
    {
        // Selon ton paramétrage YengaPay, tu peux recevoir des params.
        // On reste simple: l'utilisateur arrive sur une page "merci".
        // Le vrai statut est confirmé via webhook.
        return view('front.pages.payment_return', [
            'message' => 'Paiement en cours de confirmation. Vous serez débité si la transaction est validée.'
        ]);
        // Si tu préfères JSON:
        // return response()->json(['message' => 'Paiement en cours de confirmation.']);
    }
}
