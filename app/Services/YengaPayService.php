<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YengaPayService
{
    private function headers(): array
    {
        return [
            'x-api-key' => (string) config('services.yengapay.api_key'),
            'Accept'    => 'application/json',
        ];
    }

    public function createPaymentIntent(array $payload): array
    {
        $orgId     = (string) config('services.yengapay.organization_id');
        $projectId = (string) config('services.yengapay.project_id');
        $baseUrl   = rtrim((string) config('services.yengapay.base_url'), '/');

        $url = "{$baseUrl}/groups/{$orgId}/payment-intent/{$projectId}";

        $res = Http::timeout((int) config('services.yengapay.timeout', 20))
            ->withHeaders($this->headers())
            ->asJson()
            ->post($url, $payload);

        if (!$res->successful()) {
            Log::error('YengaPay createPaymentIntent failed', [
                'status' => $res->status(),
                'body'   => $res->body(),
                'url'    => $url,
                'payload'=> $payload,
            ]);
        }

        $res->throw();
        return (array) $res->json();
    }

    public function getPaymentIntent(string $intentId): array
    {
        $orgId     = (string) config('services.yengapay.organization_id');
        $projectId = (string) config('services.yengapay.project_id');
        $baseUrl   = rtrim((string) config('services.yengapay.base_url'), '/');

        $url = "{$baseUrl}/groups/{$orgId}/payment-intent/project/{$projectId}/intent/{$intentId}";

        $res = Http::timeout((int) config('services.yengapay.timeout', 20))
            ->withHeaders($this->headers())
            ->get($url);

        if (!$res->successful()) {
            Log::error('YengaPay getPaymentIntent failed', [
                'status' => $res->status(),
                'body'   => $res->body(),
                'url'    => $url,
                'intentId'=> $intentId,
            ]);
        }

        $res->throw();
        return (array) $res->json();
    }

    public function getMerchantPayment(string $paymentIdOrTransIdOrRef): array
    {
        $orgId     = (string) config('services.yengapay.organization_id');
        $projectId = (string) config('services.yengapay.project_id');
        $baseUrl   = rtrim((string) config('services.yengapay.base_url'), '/');

        $url = "{$baseUrl}/groups/{$orgId}/merchant-payment/project/{$projectId}/payment/{$paymentIdOrTransIdOrRef}";

        $res = Http::timeout((int) config('services.yengapay.timeout', 20))
            ->withHeaders($this->headers())
            ->get($url);

        $res->throw();
        return (array) $res->json();
    }
}
