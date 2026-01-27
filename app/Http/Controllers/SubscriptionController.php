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
    /**
     * API JSON: liste des plans
     */
    public function plans()
    {
        return response()->json(
            SubscriptionPlan::where('is_active', true)->orderBy('price')->get()
        );
    }

    /**
     * Page UI: /abonnement
     */
    public function plansPage()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        return view('front.pages.plans', compact('plans'));
    }

    /**
     * Crée un paiement d'abonnement
     */
    public function createSubscriptionPayment(Request $request, YengaPayService $yenga)
    {
        $user = $request->user();

        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::where('id', $validated['plan_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // ✅ Si déjà abonné actif, on ne crée pas un nouveau paiement
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

        // ✅ Idempotence: si paiement PENDING déjà créé récemment pour ce plan, le renvoyer
        $existingPending = Payment::where('user_id', $user->id)
            ->where('payable_type', Subscription::class)
            ->whereIn('status', ['PENDING'])
            ->orderByDesc('id')
            ->first();

        if ($existingPending && $existingPending->checkout_url) {
            return response()->json([
                'checkout_url' => $existingPending->checkout_url,
                'reference' => $existingPending->reference,
                'payment_id' => $existingPending->id,
            ], 200);
        }

        // Référence unique
        $reference = 'SUB-' . $plan->id . '-' . $user->id . '-' . Str::upper(Str::random(10));

        try {
            return DB::transaction(function () use ($user, $plan, $reference, $yenga) {

                // 1) Créer subscription pending
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => 'pending',
                    'starts_at' => null,
                    'ends_at' => null,
                    'cancelled_at' => null,
                ]);

                // 2) Créer payment local (avant l'appel externe)
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'payable_type' => Subscription::class,
                    'payable_id' => $subscription->id,

                    'provider' => 'yengapay',
                    'provider_intent_id' => null,
                    'reference' => $reference,

                    'provider_project_id' => (string) config('services.yengapay.project_id'),
                    'provider_group_id' => (string) config('services.yengapay.organization_id'),

                    // montant de base = prix du plan
                    'amount' => (float) $plan->price,
                    'fees' => 0,
                    'currency' => $plan->currency ?? 'XOF',

                    'status' => 'PENDING',
                    'is_used' => false,
                ]);

                // 3) Appel YengaPay create intent
                $payload = [
                    'paymentAmount' => (int) $plan->price,
                    'reference' => $reference,
                    'articles' => [[
                        'title' => 'Abonnement ' . $plan->name,
                        'description' => $plan->description ?: "Abonnement Fasolivre ({$plan->duration_days} jours)",
                        'pictures' => [],
                        'price' => (int) $plan->price,
                    ]],
                ];

                $data = $yenga->createPaymentIntent($payload);

                // 4) Mise à jour payment avec réponse YengaPay
                $payment->update([
                    'provider_intent_id' => $data['id'] ?? null,
                    // On garde amount = prix plan (fees séparés)
                    'fees' => (float) ($data['paymentFees'] ?? 0),
                    'currency' => (string) ($data['currency'] ?? ($plan->currency ?? 'XOF')),
                    'status' => (string) ($data['transactionStatus'] ?? 'PENDING'),
                    'token' => $data['token'] ?? null,
                    'checkout_url' => $data['checkoutPageUrlWithPaymentToken'] ?? null,
                    'provider_payload' => $data,
                ]);

                return response()->json([
                    'checkout_url' => $payment->checkout_url,
                    'reference' => $payment->reference,
                    'payment_id' => $payment->id,
                    'subscription_id' => $subscription->id,
                    'plan' => [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'price' => $plan->price,
                        'duration_days' => $plan->duration_days,
                    ],
                ], 200);
            });
        } catch (\Throwable $e) {
            // Si YengaPay échoue, la transaction annule subscription/payment
            return response()->json([
                'message' => "Impossible de générer le paiement d'abonnement.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
