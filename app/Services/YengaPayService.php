<?php
// app/Services/YengaPayService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class YengaPayService
{
  public function createPaymentIntent(array $payload): array
  {
    $orgId = config('services.yengapay.organization_id');
    $projectId = config('services.yengapay.project_id');
    $baseUrl = rtrim(config('services.yengapay.base_url'), '/');

    $url = "{$baseUrl}/groups/{$orgId}/payment-intent/{$projectId}";

    $res = Http::withHeaders([
      'x-api-key' => config('services.yengapay.api_key'),
      'Content-Type' => 'application/json',
    ])->post($url, $payload);

    $res->throw();

    return $res->json();
  }
}
