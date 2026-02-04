<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\WebhookEvent;
use App\Models\BookPurchase;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YengaPayWebhookController extends Controller
{
    // ✅ Statuts success/failed élargis (YengaPay peut varier)
    private const SUCCESS_STATUSES = [
        'SUCCESS', 'SUCCEEDED', 'COMPLETED', 'PAID', 'SUCCESSFUL', 'DONE', 'OK'
    ];

    private const FAILED_STATUSES = [
        'FAILED', 'CANCELLED', 'CANCELED', 'EXPIRED', 'REFUSED', 'ERROR'
    ];

    public function handle(Request $request)
    {
        $raw = $request->getContent();

        // (Debug utile en prod au début)
        Log::info('YENGAPAY_WEBHOOK_RECEIVED', [
            'ip' => $request->ip(),
            'headers' => $this->safeHeaders($request),
            'payload' => $request->all(),
        ]);

        // ✅ Vérif signature (si tu veux rendre strict: mets config('services...webhook_strict') = true)
        if (!$this->verifySignature($request, $raw)) {
            Log::warning('YENGAPAY_WEBHOOK_INVALID_SIGNATURE', [
                'headers' => $this->safeHeaders($request),
            ]);
            return response()->json(['ok' => false, 'error' => 'Invalid signature'], 401);
        }

        $payload = $request->all();

        // id éventuel si YengaPay en envoie
        $eventId = data_get($payload, 'eventId')
            ?? data_get($payload, 'event_id')
            ?? data_get($payload, 'id')
            ?? null;

        // fingerprint stable (idempotence si pas d'eventId)
        $eventHash = hash('sha256', $raw);

        $signature = $request->header('x-signature')
            ?? $request->header('x-yengapay-signature')
            ?? $request->header('X-YengaPay-Signature')
            ?? $request->header('x-webhook-signature');

        // 1) Stocker l’événement (idempotence DB)
        //    - si event_id présent -> unique(provider,event_id)
        //    - sinon -> unique(provider,event_hash)
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
            // Déjà reçu → OK
            Log::info('YENGAPAY_WEBHOOK_DUPLICATE', [
                'event_id' => $eventId,
                'event_hash' => $eventHash,
            ]);
            return response()->json(['ok' => true]);
        }

        $reference = (string) data_get($payload, 'reference', '');
        $statusRaw = data_get($payload, 'transactionStatus') ?? data_get($payload, 'status');
        $status = strtoupper(trim((string) $statusRaw));

        if ($reference === '') {
            $this->markEventFailed($eventHash, 'Missing reference');
            return response()->json(['ok' => true]);
        }

        DB::transaction(function () use ($reference, $status, $payload, $eventHash) {

            /** @var Payment|null $payment */
            $payment = Payment::where('reference', $reference)->lockForUpdate()->first();

            if (!$payment) {
                $this->markEventFailed($eventHash, 'Payment not found for reference: '.$reference);
                return;
            }

            // 2) Mettre à jour le paiement
            if ($status !== '') {
                $payment->status = $status;
            }
            $payment->provider_payload = $payload;

            if ($this->isSuccess($payment->status) && !$payment->paid_at) {
                $payment->paid_at = now();
            }

            $payment->save();

            // 3) Idempotence business : si déjà utilisé, stop
            if ($payment->is_used) {
                $this->markEventProcessed($eventHash);
                return;
            }

            // 4) Effets business
            if ($this->isSuccess($payment->status)) {

                // A) Achat livre => unlock
                if ($payment->payable_type === BookPurchase::class) {
                    $purchase = BookPurchase::lockForUpdate()->find($payment->payable_id);

                    if ($purchase) {
                        // ✅ très important: marquer purchased_at + lier payment_id
                        if (!$purchase->purchased_at) {
                            $purchase->purchased_at = now();
                        }
                        if (!$purchase->payment_id) {
                            $purchase->payment_id = $payment->id;
                        }
                        $purchase->save();
                    }
                }

                // B) Abonnement
                if ($payment->payable_type === Subscription::class) {
                    $sub = Subscription::lockForUpdate()->find($payment->payable_id);

                    if ($sub) {
                        $sub->loadMissing('plan');

                        $durationDays = (int) ($sub->plan->duration_days ?? 30);

                        // Si un abonnement actif existe, on prolonge le meilleur (ends_at le plus loin)
                        $currentActive = Subscription::where('user_id', $sub->user_id)
                            ->where('status', 'active')
                            ->whereNotNull('ends_at')
                            ->where('ends_at', '>', now())
                            ->orderByDesc('ends_at')
                            ->lockForUpdate()
                            ->first();

                        if ($currentActive) {
                            $currentActive->ends_at = $currentActive->ends_at->copy()->addDays($durationDays);
                            $currentActive->save();

                            // marque la nouvelle demande comme remplacée
                            if ($sub->status !== 'superseded') {
                                $sub->status = 'superseded';
                                $sub->starts_at = null;
                                $sub->ends_at = null;
                                $sub->save();
                            }
                        } else {
                            // Activation normale
                            $sub->status = 'active';
                            $sub->starts_at = now();
                            $sub->ends_at = now()->addDays($durationDays);
                            $sub->save();
                        }
                    }
                }

                // Marquer le paiement comme consommé
                $payment->is_used = true;
                $payment->save();

                $this->markEventProcessed($eventHash);
                return;
            }

            if ($this->isFailed($payment->status)) {
                // Optionnel: marquer subscription pending en failed
                if ($payment->payable_type === Subscription::class) {
                    $sub = Subscription::lockForUpdate()->find($payment->payable_id);
                    if ($sub && $sub->status === 'pending') {
                        $sub->status = 'failed';
                        $sub->save();
                    }
                }

                $this->markEventProcessed($eventHash);
                return;
            }

            // Statut inconnu/pending -> on marque processed quand même (sinon boucle infinie),
            // ou tu peux laisser "received" si tu veux rejouer.
            $this->markEventProcessed($eventHash);
        });

        return response()->json(['ok' => true]);
    }

    private function isSuccess(?string $status): bool
    {
        $s = strtoupper(trim((string) $status));
        return in_array($s, self::SUCCESS_STATUSES, true);
    }

    private function isFailed(?string $status): bool
    {
        $s = strtoupper(trim((string) $status));
        return in_array($s, self::FAILED_STATUSES, true);
    }

    private function markEventFailed(string $eventHash, string $error): void
    {
        WebhookEvent::where('provider', 'yengapay')
            ->where('event_hash', $eventHash)
            ->update([
                'processing_status' => 'failed',
                'error' => $error,
            ]);

        Log::warning('YENGAPAY_WEBHOOK_FAILED', [
            'event_hash' => $eventHash,
            'error' => $error,
        ]);
    }

    private function markEventProcessed(string $eventHash): void
    {
        WebhookEvent::where('provider', 'yengapay')
            ->where('event_hash', $eventHash)
            ->update(['processing_status' => 'processed']);
    }

    /**
     * Vérification signature:
     * - Si webhook_secret vide => autoriser (mode dev)
     * - Si strict activé => refuser si signature absente/invalide
     * - Supporte:
     *   A) header x-webhook-secret == secret
     *   B) HMAC sha256(raw, secret) comparé au header (hex ou "sha256=<hex>")
     */
    private function verifySignature(Request $request, string $raw): bool
    {
        $secret = (string) config('services.yengapay.webhook_secret', '');
        $strict = (bool) config('services.yengapay.webhook_strict', false);

        if ($secret === '') {
            return true; // pas de secret configuré
        }

        $headerSecret = (string) $request->header('x-webhook-secret', '');
        if ($headerSecret !== '') {
            return hash_equals($secret, $headerSecret);
        }

        $sig = (string) (
            $request->header('x-signature')
            ?? $request->header('x-yengapay-signature')
            ?? $request->header('X-YengaPay-Signature')
            ?? $request->header('x-webhook-signature')
            ?? ''
        );

        if ($sig === '') {
            return $strict ? false : true; // permissif tant que tu n'as pas la vraie signature
        }

        $sig = trim($sig);
        if (str_starts_with($sig, 'sha256=')) {
            $sig = substr($sig, 7);
        }

        $expected = hash_hmac('sha256', $raw, $secret);

        return hash_equals($expected, $sig);
    }

    private function safeHeaders(Request $request): array
    {
        // évite de logguer de gros headers ou sensibles inutilement
        return [
            'x-signature' => $request->header('x-signature'),
            'x-yengapay-signature' => $request->header('x-yengapay-signature'),
            'x-webhook-signature' => $request->header('x-webhook-signature'),
            'x-webhook-secret' => $request->header('x-webhook-secret') ? '***' : null,
            'content-type' => $request->header('content-type'),
            'user-agent' => $request->header('user-agent'),
        ];
    }
}
