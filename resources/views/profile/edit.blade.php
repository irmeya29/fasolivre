@extends('front.layouts.app')

@section('title', 'Mon profil – Fasolivre')

@section('content')

<style>
    :root { --faso-orange:#E0551B; --faso-green:#079C25; }

    .soft-card{
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        box-shadow: 0 10px 25px rgba(2,6,23,.06);
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-10 lg:py-12">

    {{-- HEADER --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-b from-white to-[#fff7f2] p-6 sm:p-8 mb-10">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-[var(--faso-orange)]/10 blur-3xl rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-[var(--faso-green)]/10 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-[11px] font-semibold text-slate-700">
                    <i data-lucide="user-circle" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                    Mon compte
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 flex items-center gap-3">
                    <i data-lucide="settings" class="w-8 h-8 text-[var(--faso-orange)]"></i>
                    Profil & Sécurité
                </h1>

                <p class="text-sm text-slate-600">
                    Mets à jour tes informations, ton mot de passe et tes préférences.
                </p>
            </div>

            <a href="{{ route('account.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-700 font-extrabold text-sm
                      hover:border-[var(--faso-orange)] hover:text-[var(--faso-orange)] hover:shadow-sm transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                Retour au compte
            </a>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- MENU (même logique que account) --}}
        <aside class="lg:col-span-3 space-y-3">
            <a href="{{ route('account.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border text-sm font-bold transition
                      bg-white border-slate-200 text-slate-700 hover:bg-slate-50">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Tableau de bord
            </a>

            <a href="{{ route('account.books') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border text-sm font-bold transition
                      bg-white border-slate-200 text-slate-700 hover:bg-slate-50">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                Mes livres
            </a>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border text-sm font-bold transition
                      bg-slate-900 text-white border-slate-900 shadow-sm">
                <i data-lucide="user" class="w-5 h-5"></i>
                Profil & sécurité
            </a>

            <a href="{{ route('plans.page') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border text-sm font-bold transition
                      bg-white border-slate-200 text-slate-700 hover:bg-slate-50">
                <i data-lucide="crown" class="w-5 h-5 text-[var(--faso-orange)]"></i>
                Abonnement
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl bg-red-50 text-red-600 border border-red-200
                               hover:bg-red-100 transition font-bold text-sm">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    Déconnexion
                </button>
            </form>
        </aside>

        {{-- SECTIONS --}}
        <main class="lg:col-span-9 space-y-8">

            {{-- Profil --}}
            <section class="soft-card rounded-3xl p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2">
                            <i data-lucide="id-card" class="w-5 h-5 text-[var(--faso-orange)]"></i>
                            Informations du profil
                        </h2>
                        <p class="text-sm text-slate-600 mt-1">
                            Mets à jour tes informations personnelles et ton adresse email.
                        </p>
                    </div>
                </div>

                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </section>

            {{-- Mot de passe --}}
            <section class="soft-card rounded-3xl p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2">
                            <i data-lucide="lock-keyhole" class="w-5 h-5 text-[var(--faso-green)]"></i>
                            Sécurité du compte
                        </h2>
                        <p class="text-sm text-slate-600 mt-1">
                            Change ton mot de passe pour renforcer la sécurité.
                        </p>
                    </div>
                </div>

                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </section>

            {{-- Suppression --}}
            <section class="rounded-3xl p-6 sm:p-8 border border-red-200 bg-red-50">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-lg sm:text-xl font-extrabold text-red-700 flex items-center gap-2">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                            Suppression du compte
                        </h2>
                        <p class="text-sm text-red-700/80 mt-1">
                            Attention : cette action est définitive et supprimera toutes tes données.
                        </p>
                    </div>
                </div>

                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </section>

        </main>
    </div>

</div>

@endsection
