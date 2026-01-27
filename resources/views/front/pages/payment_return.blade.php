@extends('front.layouts.app')

@section('title', 'Paiement – Fasolivre')

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

<div class="max-w-3xl mx-auto px-4 py-16">
    <div class="glass rounded-3xl p-8 shadow-xl text-center">
        <div class="mx-auto w-14 h-14 rounded-2xl flex items-center justify-center bg-slate-900 text-white">
            <i data-lucide="credit-card" class="w-7 h-7"></i>
        </div>

        <h1 class="mt-6 text-2xl font-extrabold text-slate-900">Paiement reçu</h1>
        <p class="mt-2 text-slate-600 text-sm leading-relaxed">
            Merci ! Ton paiement est en cours de confirmation.
            Dès que la transaction est validée, ton accès sera activé automatiquement.
        </p>

        @if(session('error'))
            <div class="mt-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mt-6 rounded-2xl bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('account.books') }}"
               class="px-5 py-3 rounded-2xl text-white font-semibold shadow
                      bg-gradient-to-r from-[var(--faso-orange)] to-[var(--faso-green)]">
                Aller à mes livres
            </a>

            <a href="{{ route('books.index') }}"
               class="px-5 py-3 rounded-2xl bg-white border border-slate-200 text-slate-800 font-semibold">
                Continuer à explorer
            </a>
        </div>

        <p class="mt-6 text-xs text-slate-400">
            Si tu ne vois pas ton accès après quelques instants, recharge la page ou contacte le support.
        </p>
    </div>
</div>
@endsection
