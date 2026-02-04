@extends('front.layouts.app')

@section('title', 'Mon compte – Fasolivre')

@section('content')

<style>
    :root { --faso-orange:#E0551B; --faso-green:#079C25; }

    .soft-card{
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        box-shadow: 0 10px 25px rgba(2,6,23,.06);
    }

    .clamp-2{
        display:-webkit-box;
        -webkit-line-clamp:2;
        -webkit-box-orient:vertical;
        overflow:hidden;
    }
</style>

@php
    $user = auth()->user();

    // ✅ Avatar
    $avatar = "https://ui-avatars.com/api/?name=".urlencode($user->name)."&background=ffffff&color=E0551B&size=256";

    // ✅ Abonnement actif ?
    $activeSub = \App\Models\Subscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->whereNotNull('ends_at')
        ->where('ends_at', '>', now())
        ->first();

    // ✅ Derniers achats confirmés
    $recentPurchases = \App\Models\BookPurchase::with(['book.author'])
        ->where('user_id', $user->id)
        ->whereNotNull('purchased_at')
        ->latest('purchased_at')
        ->take(3)
        ->get();

    $ownedCount = \App\Models\BookPurchase::where('user_id', $user->id)
        ->whereNotNull('purchased_at')
        ->count();
@endphp

<div class="max-w-7xl mx-auto px-4 py-10 lg:py-12">

    {{-- HEADER (pro, pas trop chargé) --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-b from-white to-[#fff7f2] p-6 sm:p-8 mb-10">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-[var(--faso-orange)]/10 blur-3xl rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-[var(--faso-green)]/10 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-4">
                <img src="{{ $avatar }}"
                     class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border border-white shadow-sm object-cover"
                     alt="{{ $user->name }}">

                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-[11px] font-semibold text-slate-700">
                        <i data-lucide="sparkles" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                        Mon compte
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                        {{ $user->name }}
                    </h1>

                    <p class="text-sm text-slate-600">
                        Bienvenue sur Fasolivre 📚
                    </p>
                </div>
            </div>

            {{-- mini stats (vraies, utiles) --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 w-full md:w-auto">
                <div class="soft-card rounded-2xl p-4 text-center">
                    <p class="text-xl font-extrabold text-slate-900">{{ $ownedCount }}</p>
                    <p class="text-[11px] font-semibold text-slate-500">Livres achetés</p>
                </div>
                <div class="soft-card rounded-2xl p-4 text-center">
                    <p class="text-xl font-extrabold text-slate-900">{{ $activeSub ? 'Actif' : '—' }}</p>
                    <p class="text-[11px] font-semibold text-slate-500">Abonnement</p>
                </div>
                <div class="hidden sm:block soft-card rounded-2xl p-4 text-center">
                    <p class="text-xl font-extrabold text-slate-900">∞</p>
                    <p class="text-[11px] font-semibold text-slate-500">Accès lecture</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- SIDEBAR (navigation) --}}
        <aside class="lg:col-span-3 space-y-3">
            <a href="{{ route('account.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border text-sm font-bold transition
               {{ request()->routeIs('account.index')
                    ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                    : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Tableau de bord
            </a>

            <a href="{{ route('account.books') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border text-sm font-bold transition
               {{ request()->routeIs('account.books')
                    ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                    : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                Mes livres
            </a>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border text-sm font-bold transition
               {{ request()->routeIs('profile.edit')
                    ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                    : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
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

        {{-- CONTENT --}}
        <main class="lg:col-span-9 space-y-8">

            {{-- 1) Carte Abonnement (utile) --}}
            <div class="soft-card rounded-3xl p-6 sm:p-7">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                            <i data-lucide="crown" class="w-5 h-5 text-[var(--faso-orange)]"></i>
                            Abonnement
                        </h2>

                        @if($activeSub)
                            <p class="text-sm text-slate-600">
                                Statut : <span class="font-bold text-[var(--faso-green)]">Actif</span>
                                • Expire le <span class="font-bold">{{ \Carbon\Carbon::parse($activeSub->ends_at)->format('d/m/Y') }}</span>
                            </p>
                        @else
                            <p class="text-sm text-slate-600">
                                Aucun abonnement actif. Accède à plus de livres avec un abonnement.
                            </p>
                        @endif
                    </div>

                    <a href="{{ route('plans.page') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[var(--faso-orange)] text-white font-extrabold text-sm
                              hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                        Voir les abonnements
                    </a>
                </div>
            </div>

            {{-- 2) Derniers livres (3 max) --}}
            <div class="soft-card rounded-3xl p-6 sm:p-7">
                <div class="flex items-center justify-between gap-4 mb-5">
                    <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <i data-lucide="library" class="w-5 h-5 text-[var(--faso-orange)]"></i>
                        Derniers livres
                    </h2>

                    <a href="{{ route('account.books') }}"
                       class="text-sm font-bold text-[var(--faso-orange)] hover:underline inline-flex items-center gap-1">
                        Tout voir <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                @if($recentPurchases->count())
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($recentPurchases as $purchase)
                            @php
                                $book = $purchase->book;
                                $cover = $book && $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');
                            @endphp

                            @if($book)
                                <a href="{{ route('books.show', $book->slug) }}"
                                   class="group bg-white border border-slate-100 rounded-3xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition">
                                    <div class="relative">
                                        <img src="{{ $cover }}" class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300" alt="{{ $book->title }}">
                                        <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                                            <span class="px-3 py-1.5 rounded-full bg-white/90 text-slate-900 text-[11px] font-extrabold backdrop-blur border border-white/60">
                                                Acheté
                                            </span>

                                            @if($book->pdf_file)
                                                <span class="px-3 py-1.5 rounded-full bg-[var(--faso-green)] text-white text-[11px] font-extrabold">
                                                    Lire
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-4 space-y-1">
                                        <p class="font-extrabold text-slate-900 text-sm clamp-2">{{ $book->title }}</p>
                                        <p class="text-[11px] text-slate-500 inline-flex items-center gap-1">
                                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                            {{ optional($book->author)->name ?? 'Auteur' }}
                                        </p>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-slate-100 rounded-3xl p-8 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                            <i data-lucide="shopping-bag" class="w-6 h-6 text-slate-500"></i>
                        </div>
                        <p class="font-extrabold text-slate-900">Aucun achat pour le moment</p>
                        <p class="text-sm text-slate-600 mt-1">Explore la bibliothèque et commence ta prochaine lecture.</p>

                        <a href="{{ route('books.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold text-sm
                                  hover:bg-slate-800 transition">
                            <i data-lucide="library" class="w-5 h-5"></i>
                            Explorer les livres
                        </a>
                    </div>
                @endif
            </div>

            {{-- 3) Raccourcis (max 2-3, pas redondant) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('books.index') }}"
                   class="soft-card rounded-3xl p-5 hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#E0551B]/10 flex items-center justify-center mb-3">
                        <i data-lucide="search" class="w-6 h-6 text-[#E0551B]"></i>
                    </div>
                    <p class="font-extrabold text-slate-900">Trouver un livre</p>
                    <p class="text-sm text-slate-600 mt-1">Découvrir les nouveautés.</p>
                </a>

                <a href="{{ route('plans.page') }}"
                   class="soft-card rounded-3xl p-5 hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#079C25]/10 flex items-center justify-center mb-3">
                        <i data-lucide="crown" class="w-6 h-6 text-[#079C25]"></i>
                    </div>
                    <p class="font-extrabold text-slate-900">Passer en Premium</p>
                    <p class="text-sm text-slate-600 mt-1">Accès à plus de livres.</p>
                </a>

                <a href="{{ url('/submit') }}"
                   class="soft-card rounded-3xl p-5 hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                        <i data-lucide="file-plus" class="w-6 h-6 text-slate-700"></i>
                    </div>
                    <p class="font-extrabold text-slate-900">Soumettre un manuscrit</p>
                    <p class="text-sm text-slate-600 mt-1">Publier avec Fasolivre.</p>
                </a>
            </div>

        </main>
    </div>
</div>

@endsection
