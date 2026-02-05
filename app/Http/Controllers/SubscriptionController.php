<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\YengaPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function plans()
    {
        return response()->json(
            SubscriptionPlan::where('is_active', true)->orderBy('price')->get()
        );
    }

    public function plansPage()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        return view('front.pages.plans', compact('plans'));
    }

    public function createSubscriptionPayment(Request $request, YengaPayService $yenga)
    {
        $user = $request->user();

        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::where('id', $validated['plan_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $hasActiveSub = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();

        if ($hasActiveSub) {
            return response()->json([
                'message' => "Vous avez déjà un abonnement actif."
            ], 200);
        }

        $existingPending = Payment::where('user_id', $user->id)
            ->where('payable_type', Subscription::class)
            ->whereIn('status', ['PENDING'])
            ->orderByDesc('id')
            ->first();

        if ($existingPending && $existingPending->checkout_url) {
            return response()->json([
                'checkout_url' => $existingPending->checkout_url,
                'reference'    => $existingPending->reference,
                'payment_id'   => $existingPending->id,
            ], 200);
        }

        $reference   = 'SUB-' . $plan->id . '-' . $user->id . '-' . Str::upper(Str::random(10));
        $redirectUrl = route('payment.return', ['reference' => $reference], true);

        try {
            return DB::transaction(function () use ($user, $plan, $reference, $redirectUrl, $yenga) {

                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => 'pending',
                    'starts_at' => null,
                    'ends_at' => null,
                    'cancelled_at' => null,
                ]);

                $payment = Payment::create([
                    'user_id' => $user->id,
                    'payable_type' => Subscription::class,
                    'payable_id' => $subscription->id,

                    'provider' => 'yengapay',
                    'provider_intent_id' => null,
                    'reference' => $reference,

                    'provider_project_id' => (string) config('services.yengapay.project_id'),
                    'provider_group_id' => (string) config('services.yengapay.organization_id'),

                    // ✅ client paie frais : amount = prix plan
                    'amount' => (float) $plan->price,
                    'fees' => 0,
                    'currency' => $plan->currency ?? 'XOF',

                    'status' => 'PENDING',
                    'is_used' => false,
                ]);

                $payload = [
                    'paymentAmount' => (int) $plan->price,
                    'reference'     => $reference,
                    'redirectUrl'   => $redirectUrl,
                    'articles' => [[
                        'title' => 'Abonnement ' . $plan->name,
                        'description' => $plan->description ?: "Abonnement Fasolivre ({$plan->duration_days} jours)",
                        'pictures' => [],
                        'price' => (int) $plan->price,
                    ]],
                ];

                $data = $yenga->createPaymentIntent($payload);

                $statusRaw = $data['paymentStatus'] ?? $data['transactionStatus'] ?? $data['status'] ?? 'PENDING';
                $statusUp  = strtoupper((string) $statusRaw);

                $mapped = 'PENDING';
                if (in_array($statusUp, ['DONE','SUCCESS','SUCCESSFUL','PAID','COMPLETED','OK','APPROVED'], true)) $mapped = 'SUCCESS';
                elseif (in_array($statusUp, ['FAILED','CANCELLED','CANCELED','EXPIRED','REFUSED','REJECTED'], true)) $mapped = 'FAILED';

                $payment->update([
                    'provider_intent_id' => $data['id'] ?? null,
                    'fees' => (float) ($data['paymentFees'] ?? 0),
                    'currency' => (string) ($data['currency'] ?? ($plan->currency ?? 'XOF')),
                    'status' => $mapped,
                    'token' => $data['token'] ?? null,
                    'checkout_url' => $data['checkoutPageUrlWithPaymentToken'] ?? null,
                    'provider_payload' => $data,
                ]);

                return response()->json([
                    'checkout_url' => $payment->checkout_url,
                    'reference' => $payment->reference,
                    'payment_id' => $payment->id,
                    'subscription_id' => $subscription->id,
                ], 200);
            });
        } catch (\Throwable $e) {
            return response()->json([
                'message' => "Impossible de générer le paiement d'abonnement.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
