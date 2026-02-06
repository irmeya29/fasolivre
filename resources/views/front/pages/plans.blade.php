@extends('front.layouts.app')

@section('title', 'Abonnement – Fasolivre')

@section('content')
<style>
    :root { --faso-orange:#E0551B; --faso-green:#079C25; }
    .glass{
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        background: rgba(255,255,255,.65);
        border: 1px solid rgba(255,255,255,.45);
    }
</style>

<div class="max-w-6xl mx-auto px-4 py-12">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 flex items-center gap-3">
                <i data-lucide="crown" class="w-8 h-8 text-[var(--faso-orange)]"></i>
                Choisir un abonnement
            </h1>
            <p class="mt-2 text-sm text-slate-600 max-w-2xl">
                Avec un abonnement actif, tu as accès à tous les livres marqués <strong>Abonnement</strong>.
            </p>
        </div>

        @auth
            <a href="{{ route('account.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white border border-slate-200 text-slate-800 font-semibold">
                <i data-lucide="user" class="w-4 h-4"></i>
                Mon compte
            </a>
        @else
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                <i data-lucide="lock" class="w-4 h-4"></i>
                Se connecter
            </a>
        @endauth
    </div>

    {{-- Alert --}}
    <div id="plan-alert" class="hidden mb-8 rounded-2xl border p-4 text-sm"></div>

    @if(empty($plans) || $plans->count() === 0)
        <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center text-slate-600">
            <div class="text-lg font-bold text-slate-900">Aucun plan disponible</div>
            <div class="mt-1 text-sm">Ajoute des plans dans la table <code>subscription_plans</code> (seeder).</div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="glass rounded-3xl p-7 shadow-lg">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">{{ $plan->name }}</h3>
                            <p class="text-sm text-slate-600 mt-1">
                                {{ $plan->description ?? 'Accès à tous les livres abonnement.' }}
                            </p>
                        </div>

                        <span class="px-3 py-1 rounded-xl bg-slate-900 text-white text-xs font-semibold">
                            {{ (int)$plan->duration_days }} jours
                        </span>
                    </div>

                    <div class="mt-6 flex items-end justify-between">
                        <div>
                            <div class="text-3xl font-extrabold text-slate-900">
                                {{ number_format((float)$plan->price, 0, ',', ' ') }}
                            </div>
                            <div class="text-xs text-slate-500 -mt-1">
                                {{ $plan->currency ?? 'XOF' }}
                            </div>
                        </div>

                        @auth
                            <button
                                type="button"
                                class="subscribe-btn px-5 py-3 rounded-2xl text-white font-semibold shadow
                                       bg-gradient-to-r from-[var(--faso-orange)] to-[var(--faso-green)]"
                                data-plan-id="{{ $plan->id }}">
                                S’abonner
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                               class="px-5 py-3 rounded-2xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                Se connecter
                            </a>
                        @endauth
                    </div>

                    <div class="mt-6 text-xs text-slate-500 leading-relaxed">
                        Le paiement est confirmé automatiquement via webhook.
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@auth
<script>
document.addEventListener('DOMContentLoaded', () => {
    const alertBox = document.getElementById('plan-alert');

    const showAlert = (type, msg) => {
        alertBox.classList.remove('hidden');
        alertBox.className = 'mb-8 rounded-2xl border p-4 text-sm ' + (
            type === 'success'
                ? 'bg-green-50 border-green-200 text-green-800'
                : 'bg-red-50 border-red-200 text-red-800'
        );
        alertBox.textContent = msg;
    };

    document.querySelectorAll('.subscribe-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const planId = btn.getAttribute('data-plan-id');
            btn.disabled = true;

            try {
                const res = await fetch("{{ route('pay.subscription') }}", {
                    method: "POST",
                    credentials: "same-origin", // ✅ important (session/cookies)
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ plan_id: parseInt(planId, 10) })
                });

                const text = await res.text();
                let data = {};
                try { data = JSON.parse(text); } catch (e) {}

                if (!res.ok) {
                    if (res.status === 401) {
                        window.location.href = "{{ route('login', ['redirect' => url()->current()]) }}";
                        return;
                    }
                    if (res.status === 419) {
                        showAlert('error', "Session expirée. Rafraîchis la page puis réessaie.");
                        return;
                    }
                    console.error("SUBSCRIBE ERROR", res.status, text);
                    showAlert('error', (data.message ?? "Impossible de générer le paiement.") + " (HTTP " + res.status + ")");
                    return;
                }

                if (data.checkout_url) {
                    window.location.href = data.checkout_url;
                    return;
                }

                showAlert('error', data.message ?? "Impossible de générer le paiement.");
            } catch (e) {
                showAlert('error', "Erreur réseau. Réessaie.");
            } finally {
                btn.disabled = false;
            }
        });
    });
});
</script>
@endauth
@endsection
