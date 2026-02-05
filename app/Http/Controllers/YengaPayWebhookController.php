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
    private const SUCCESS_STATUSES = [
        'DONE', // ✅ YengaPay
        'SUCCESS', 'SUCCESSFUL', 'SUCCEEDED', 'COMPLETED', 'PAID', 'OK', 'APPROVED'
    ];

    private const FAILED_STATUSES  = [
        'FAILED', 'CANCELLED', 'CANCELED', 'EXPIRED', 'REFUSED', 'REJECTED'
    ];

    public function handle(Request $request)
    {
        $raw = $request->getContent();
        $payload = $request->all();

        if (!$this->verifySignature($request)) {
            Log::warning('YengaPay webhook invalid signature', [
                'headers' => $request->headers->all(),
                'payload' => $payload,
            ]);
            return response()->json(['ok' => false, 'error' => 'Invalid signature'], 401);
        }

        $eventId = data_get($payload, 'eventId')
            ?? data_get($payload, 'id')
            ?? data_get($payload, 'event_id')
            ?? null;

        // idempotence hash
        $eventHash = hash('sha256', $raw);

        $signature = (string) $request->header('x-webhook-hash', '');

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
            return response()->json(['ok' => true]); // déjà reçu
        }

        // ✅ reference robuste
        $reference =
            data_get($payload, 'reference')
            ?? data_get($payload, 'data.reference')
            ?? data_get($payload, 'transaction.reference')
            ?? data_get($payload, 'payment.reference')
            ?? data_get($payload, 'merchantReference');

        // ✅ status robuste (YengaPay: paymentStatus)
        $statusRaw =
            data_get($payload, 'paymentStatus')
            ?? data_get($payload, 'transactionStatus')
            ?? data_get($payload, 'status')
            ?? data_get($payload, 'data.status')
            ?? data_get($payload, 'transaction.status');

        $status = strtoupper((string) $statusRaw);

        if (!$reference) {
            WebhookEvent::where('provider', 'yengapay')->where('event_hash', $eventHash)
                ->update(['processing_status' => 'failed', 'error' => 'Missing reference']);
            return response()->json(['ok' => true]);
        }

        DB::transaction(function () use ($reference, $status, $payload, $eventHash) {

            $payment = Payment::where('reference', $reference)->lockForUpdate()->first();

            if (!$payment) {
                WebhookEvent::where('provider', 'yengapay')->where('event_hash', $eventHash)
                    ->update(['processing_status' => 'failed', 'error' => 'Payment not found for reference: '.$reference]);

                Log::warning('Webhook payment not found', ['reference' => $reference, 'payload' => $payload]);
                return;
            }

            // update payment record
            if ($status !== '') $payment->status = $status;
            $payment->provider_payload = $payload;

            if (in_array($payment->status, self::SUCCESS_STATUSES, true) && !$payment->paid_at) {
                $payment->paid_at = now();
            }
            $payment->save();

            // business idempotence
            if ($payment->is_used) {
                WebhookEvent::where('provider', 'yengapay')->where('event_hash', $eventHash)
                    ->update(['processing_status' => 'processed']);
                return;
            }

            // SUCCESS
            if (in_array($payment->status, self::SUCCESS_STATUSES, true)) {

                // A) Book purchase unlock
                if ($payment->payable_type === BookPurchase::class) {
                    $purchase = BookPurchase::lockForUpdate()->find($payment->payable_id);

                    if ($purchase && !$purchase->purchased_at) {
                        $purchase->purchased_at = now();
                        $purchase->payment_id = $payment->id;
                        $purchase->save();
                    }
                }

                // B) Subscription activation (si tu l'utilises)
                if ($payment->payable_type === Subscription::class) {
                    $sub = Subscription::lockForUpdate()->find($payment->payable_id);

                    if ($sub) {
                        $sub->loadMissing('plan');
                        $days = (int) ($sub->plan->duration_days ?? 30);

                        $currentActive = Subscription::where('user_id', $sub->user_id)
                            ->where('status', 'active')
                            ->whereNotNull('ends_at')
                            ->where('ends_at', '>', now())
                            ->orderByDesc('ends_at')
                            ->lockForUpdate()
                            ->first();

                        if ($currentActive) {
                            $currentActive->ends_at = $currentActive->ends_at->copy()->addDays($days);
                            $currentActive->save();

                            $sub->status = 'superseded';
                            $sub->starts_at = null;
                            $sub->ends_at = null;
                            $sub->save();
                        } else {
                            $sub->status = 'active';
                            $sub->starts_at = now();
                            $sub->ends_at = now()->addDays($days);
                            $sub->save();
                        }
                    }
                }

                $payment->is_used = true;
                $payment->save();
            }

            // FAILED
            if (in_array($payment->status, self::FAILED_STATUSES, true)) {
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
     * ✅ YengaPay doc: x-webhook-hash = HMAC_SHA256(json(payload), secret)
     */
    private function verifySignature(Request $request): bool
    {
        $secret = (string) config('services.yengapay.webhook_secret');
        if ($secret === '') return true;

        $hash = (string) $request->header('x-webhook-hash', '');
        if ($hash === '') return false;

        $data = json_encode($request->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $expected = hash_hmac('sha256', $data, $secret);

        return hash_equals($expected, $hash);
    }
}
