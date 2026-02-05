<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YengaPayService
{
    private function headers(): array
    {
        return [
            'x-api-key' => (string) config('services.yengapay.api_key'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function createPaymentIntent(array $payload): array
    {
        $orgId     = (string) config('services.yengapay.organization_id');
        $projectId = (string) config('services.yengapay.project_id');
        $baseUrl   = rtrim((string) config('services.yengapay.base_url'), '/');

        $url = "{$baseUrl}/groups/{$orgId}/payment-intent/{$projectId}";

        // ✅ Nettoyage payload (évite erreurs YengaPay)
        // - paymentAmount doit être int
        // - supprime currency si pas supporté
        // - pictures -> max 1-2 images et URL https
        if (isset($payload['paymentAmount'])) {
            $payload['paymentAmount'] = (int) $payload['paymentAmount'];
        }

        if (isset($payload['articles']) && is_array($payload['articles'])) {
            foreach ($payload['articles'] as $i => $a) {
                if (isset($a['price'])) $payload['articles'][$i]['price'] = (int) $a['price'];
                if (!isset($payload['articles'][$i]['pictures']) || !is_array($payload['articles'][$i]['pictures'])) {
                    $payload['articles'][$i]['pictures'] = [];
                }
                // garde max 1 image et uniquement https (sécurité)
                $pics = array_values(array_filter($payload['articles'][$i]['pictures'], fn($p) => is_string($p) && str_starts_with($p, 'https://')));
                $payload['articles'][$i]['pictures'] = array_slice($pics, 0, 1);
            }
        }

        // ⚠️ Certains projets YengaPay n'aiment pas currency dans create intent
        unset($payload['currency']);

        try {
            $res = Http::timeout(30)
                ->withHeaders($this->headers())
                ->post($url, $payload);

            if (!$res->successful()) {
                Log::error('YengaPay createPaymentIntent failed', [
                    'status'  => $res->status(),
                    'body'    => $res->body(),
                    'url'     => $url,
                    'payload' => $payload,
                ]);
                $res->throw();
            }

            return (array) $res->json();
        } catch (RequestException $e) {
            // on relance, controller gère
            throw $e;
        }
    }

    public function getPaymentIntent(string $intentId): array
    {
        $orgId     = (string) config('services.yengapay.organization_id');
        $projectId = (string) config('services.yengapay.project_id');
        $baseUrl   = rtrim((string) config('services.yengapay.base_url'), '/');

        $url = "{$baseUrl}/groups/{$orgId}/payment-intent/project/{$projectId}/intent/{$intentId}";

        $res = Http::timeout(30)
            ->withHeaders($this->headers())
            ->get($url);

        if (!$res->successful()) {
            Log::error('YengaPay getPaymentIntent failed', [
                'status' => $res->status(),
                'body'   => $res->body(),
                'url'    => $url,
                'intent' => $intentId,
            ]);
            $res->throw();
        }

        return (array) $res->json();
    }
}
