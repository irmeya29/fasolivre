<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\WebhookEvent;
use App\Models\BookPurchase;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YengaPayWebhookController extends Controller
{
    // Adapte la liste exacte à la doc YengaPay si besoin
    private const SUCCESS_STATUSES = ['SUCCESS', 'SUCCEEDED', 'COMPLETED', 'PAID'];
    private const FAILED_STATUSES  = ['FAILED', 'CANCELLED', 'CANCELED', 'EXPIRED', 'REFUSED'];

    public function handle(Request $request)
    {
        $raw = $request->getContent();

        // ✅ Vérif signature (si secret présent)
        if (!$this->verifySignature($request, $raw)) {
            return response()->json(['ok' => false, 'error' => 'Invalid signature'], 401);
        }

        $payload = $request->all();

        // Essaye d’attraper un id événement si YengaPay en envoie un
        $eventId = data_get($payload, 'eventId')
            ?? data_get($payload, 'id')
            ?? data_get($payload, 'event_id')
            ?? null;

        // ✅ Fingerprint idempotence si eventId absent (MySQL autorise plusieurs NULL en unique)
        $eventHash = hash('sha256', $raw);

        // Signature header si dispo
        $signature = $request->header('x-signature')
            ?? $request->header('x-yengapay-signature')
            ?? $request->header('X-YengaPay-Signature')
            ?? $request->header('x-webhook-signature');

        // 1) Stocker événement (idempotence)
        try {
            WebhookEvent::create([
                'provider' => 'yengapay',
                'event_id' => $eventId,
                'event_hash' => $eventHash,
                'signature' => $signature,
                'payload' => $payload,
                'processing_status' => 'received',
            ]);
        } catch (\Throwable $e) {
            // Déjà reçu → OK idempotent
            return response()->json(['ok' => true]);
        }

        $reference = data_get($payload, 'reference');
        $status = strtoupper((string)(data_get($payload, 'transactionStatus') ?? data_get($payload, 'status') ?? ''));

        if (!$reference) {
            WebhookEvent::where('provider', 'yengapay')->where('event_hash', $eventHash)
                ->update(['processing_status' => 'failed', 'error' => 'Missing reference']);
            return response()->json(['ok' => true]);
        }

        DB::transaction(function () use ($reference, $status, $payload, $eventHash) {

            /** @var Payment|null $payment */
            $payment = Payment::where('reference', $reference)->lockForUpdate()->first();

            if (!$payment) {
                WebhookEvent::where('provider', 'yengapay')->where('event_hash', $eventHash)
                    ->update(['processing_status' => 'failed', 'error' => 'Payment not found']);
                return;
            }

            // 2) Update paiement
            $payment->status = $status ?: $payment->status;
            $payment->provider_payload = $payload;

            if (in_array($payment->status, self::SUCCESS_STATUSES, true) && !$payment->paid_at) {
                $payment->paid_at = now();
            }
            $payment->save();

            // 3) Idempotence business
            if ($payment->is_used) {
                WebhookEvent::where('provider', 'yengapay')->where('event_hash', $eventHash)
                    ->update(['processing_status' => 'processed']);
                return;
            }

            // 4) Effets business selon statut
            if (in_array($payment->status, self::SUCCESS_STATUSES, true)) {

                // A) Achat livre
                if ($payment->payable_type === BookPurchase::class) {
                    $purchase = BookPurchase::lockForUpdate()->find($payment->payable_id);
                    if ($purchase && !$purchase->purchased_at) {
                        $purchase->purchased_at = now();
                        $purchase->save();
                    }
                }

                // B) Abonnement
                if ($payment->payable_type === Subscription::class) {
                    $sub = Subscription::lockForUpdate()->find($payment->payable_id);

                    if ($sub && $sub->status !== 'active') {
                        $sub->loadMissing('plan');

                        $durationDays = (int)($sub->plan->duration_days ?? 30);

                        // ✅ Prolongation optionnelle (recommandée)
                        // Si user a déjà un abonnement actif, on prolonge le meilleur “ends_at”
                        $currentActive = Subscription::where('user_id', $sub->user_id)
                            ->where('status', 'active')
                            ->whereNotNull('ends_at')
                            ->where('ends_at', '>', now())
                            ->orderByDesc('ends_at')
                            ->lockForUpdate()
                            ->first();

                        if ($currentActive) {
                            // On prolonge l’abonnement actif existant
                            $currentActive->ends_at = $currentActive->ends_at->copy()->addDays($durationDays);
                            $currentActive->save();

                            // Et on marque le nouveau comme “superseded” pour éviter 2 actifs
                            $sub->status = 'superseded';
                            $sub->starts_at = null;
                            $sub->ends_at = null;
                            $sub->save();
                        } else {
                            // Activation normale
                            $sub->status = 'active';
                            $sub->starts_at = now();
                            $sub->ends_at = now()->addDays($durationDays);
                            $sub->save();
                        }
                    }
                }

                $payment->is_used = true;
                $payment->save();
            }

            if (in_array($payment->status, self::FAILED_STATUSES, true)) {
                // Optionnel: marquer subscription pending en failed/cancelled
                if ($payment->payable_type === Subscription::class) {
                    $sub = Subscription::lockForUpdate()->find($payment->payable_id);
                    if ($sub && $sub->status === 'pending') {
                        $sub->status = 'failed';
                        $sub->save();
                    }
                }
            }

            WebhookEvent::where('provider', 'yengapay')->where('event_hash', $eventHash)
                ->update(['processing_status' => 'processed']);
        });

        return response()->json(['ok' => true]);
    }

    /**
     * Vérif signature:
     * - Mode A: header x-webhook-secret == secret
     * - Mode B: HMAC sha256(raw, secret) comparé au header (hex ou "sha256=<hex>")
     */
    private function verifySignature(Request $request, string $raw): bool
    {
        $secret = (string) config('services.yengapay.webhook_secret');

        // Si tu n’as pas encore activé/confirmé la signature côté YengaPay,
        // tu peux temporairement retourner true quand secret vide.
        if ($secret === '') {
            return true;
        }

        $sig = $request->header('x-signature')
            ?? $request->header('x-yengapay-signature')
            ?? $request->header('X-YengaPay-Signature')
            ?? $request->header('x-webhook-signature')
            ?? '';

        $headerSecret = (string) $request->header('x-webhook-secret', '');

        // Mode A: secret direct
        if ($headerSecret !== '') {
            return hash_equals($secret, $headerSecret);
        }

        // Mode B: HMAC
        if ($sig === '') {
            return false;
        }

        $sig = trim($sig);
        if (str_starts_with($sig, 'sha256=')) {
            $sig = substr($sig, 7);
        }

        $expected = hash_hmac('sha256', $raw, $secret);

        return hash_equals($expected, $sig);
    }
}
