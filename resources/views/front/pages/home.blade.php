@extends('front.layouts.app')

@section('title', 'Fasolivre – Lire les auteurs africains en numérique')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
        --faso-gold:  #DCAE81;
        --faso-dark:  #3E3E3E;
    }

    .floating { animation: float 6s ease-in-out infinite; }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
</style>

{{-- ============================================================
     HERO — CLEAN PREMIUM
============================================================ --}}
<section class="relative overflow-hidden pt-12 pb-14 lg:pt-20 lg:pb-20 bg-gradient-to-b from-white via-[#fff7f2] to-white">

    {{-- glows doux --}}
    <div class="absolute inset-0 pointer-events-none -z-10">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-[var(--faso-orange)]/15 blur-3xl rounded-full"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-[var(--faso-green)]/12 blur-3xl rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 lg:px-6 grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">

        {{-- LEFT --}}
        <div class="space-y-6">
            <div class="inline-flex items-center gap-2 bg-white/90 border border-orange-200/70 text-[var(--faso-orange)]
                        shadow-sm px-4 py-1.5 rounded-full text-[11px] font-semibold backdrop-blur">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[var(--faso-green)]"></i>
                Plateforme africaine de lecture numérique
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.05] tracking-tight">
                La bibliothèque
                <span class="text-[var(--faso-orange)]">100% africaine</span>
                à portée de main.
            </h1>

            <p class="text-sm sm:text-base lg:text-lg text-slate-600 max-w-xl leading-relaxed">
                Romans, essais, poésie, BD, ouvrages académiques… Fasolivre rassemble les voix d’auteurs d’Afrique et de la diaspora, dans une expérience de lecture moderne et accessible.
            </p>

            {{-- ACTIONS --}}
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="{{ route('books.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm font-semibold text-white
                          bg-[var(--faso-orange)]
                          shadow-lg shadow-[var(--faso-orange)]/25
                          hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0">
                    <i data-lucide="library" class="w-5 h-5"></i>
                    Explorer les livres
                </a>

                <a href="{{ url('/submit') }}"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-sm font-semibold
                          border border-slate-200 text-slate-800 bg-white hover:bg-slate-50
                          shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0">
                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                    Publier mon manuscrit
                </a>
            </div>

            {{-- QUICK STATS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-5">
                <div class="bg-white/80 border border-slate-100 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center gap-2 font-semibold text-slate-900">
                        <i data-lucide="book-open" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                        <span>+100</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Livres numériques disponibles</p>
                </div>

                <div class="bg-white/80 border border-slate-100 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center gap-2 font-semibold text-slate-900">
                        <i data-lucide="users" class="w-4 h-4 text-[var(--faso-green)]"></i>
                        <span>Auteurs</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Afrique & diaspora</p>
                </div>

                <div class="bg-white/80 border border-slate-100 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center gap-2 font-semibold text-slate-900">
                        <i data-lucide="smartphone" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                        <span>Bientôt</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">App mobile Fasolivre</p>
                </div>
            </div>
        </div>

        {{-- RIGHT — Preview vedette --}}
        <div class="relative floating">
            <div class="absolute -top-6 -left-6 w-28 h-28 bg-white/60 blur-3xl rounded-full"></div>
            <div class="absolute bottom-0 -right-8 w-44 h-44 bg-[var(--faso-green)]/20 blur-3xl rounded-full"></div>

            <div class="bg-white/80 backdrop-blur-xl border border-white/70 shadow-2xl rounded-3xl p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                        <i data-lucide="flame" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                        En vedette
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full bg-orange-50 text-[var(--faso-orange)] font-medium">
                        Dernières parutions
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-3 text-xs">
                    @foreach(\App\Models\Book::where('status','published')->latest()->take(3)->get() as $book)
                        <a href="{{ route('books.show', $book->slug) }}"
                           class="group bg-slate-50 hover:bg-white rounded-2xl p-2 shadow-sm hover:shadow-md
                                  hover:-translate-y-0.5 active:translate-y-0 transition flex flex-col gap-2">

                            <div class="relative">
                                <img src="{{ asset('storage/'.$book->cover) }}"
                                     loading="lazy"
                                     class="rounded-xl aspect-[3/4] object-cover w-full group-hover:scale-[1.03] transition">
                                <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded-full bg-black/50 text-[9px] text-white">
                                    {{ strtoupper($book->format ?? 'PDF') }}
                                </span>
                            </div>

                            <p class="font-semibold text-slate-900 truncate">
                                {{ \Illuminate\Support\Str::limit($book->title, 26) }}
                            </p>

                            <p class="text-[10px] text-slate-500 truncate flex items-center gap-1">
                                <i data-lucide="user" class="w-3 h-3"></i>
                                {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                            </p>
                        </a>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 pt-3 flex items-center justify-between gap-3 text-[11px] text-slate-500">
                    <div>
                        <p class="font-medium text-slate-800">Lecture en ligne & téléchargement</p>
                        <p class="text-[10px]">Accède à tes livres partout, à tout moment.</p>
                    </div>

                    <div class="hidden sm:flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100">
                            <i data-lucide="shield-check" class="w-3 h-3 text-[var(--faso-green)]"></i>
                            Sécurisé
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100">
                            <i data-lucide="globe-2" class="w-3 h-3 text-[var(--faso-orange)]"></i>
                            Afrique
                        </span>
                    </div>
                </div>
            </div>

            <div class="hidden sm:flex mt-4 items-center gap-3 bg-white/80 backdrop-blur border border-slate-100 rounded-2xl px-4 py-3 shadow-sm">
                <i data-lucide="sparkles" class="w-4 h-4 text-[var(--faso-green)]"></i>
                <p class="text-[11px] text-slate-600">
                    Rejoins Fasolivre dès maintenant et profite d’une nouvelle expérience de lecture africaine.
                </p>
            </div>
        </div>

    </div>
</section>

{{-- ============================================================
     BARRE D’ACCÈS RAPIDE (chips)
============================================================ --}}
<section class="pb-6">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="px-3 py-1 rounded-full bg-white shadow-sm border border-slate-100 flex items-center gap-2">
                <i data-lucide="sparkles" class="w-3 h-3 text-[var(--faso-orange)]"></i>
                Sélection du moment
            </span>
            <a href="{{ route('books.index') }}" class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center gap-2">
                <i data-lucide="book-open" class="w-3 h-3"></i>
                Tout voir
            </a>
            <a href="{{ route('categories.index.front') }}" class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center gap-2">
                <i data-lucide="grid-3x3" class="w-3 h-3"></i>
                Catégories
            </a>
            <a href="{{ route('authors.index.front') }}" class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center gap-2">
                <i data-lucide="pen-square" class="w-3 h-3"></i>
                Auteurs
            </a>
        </div>
    </div>
</section>

{{-- ============================================================
     DERNIERS LIVRES — CARTES VENDEUSES (prix visible)
============================================================ --}}
<section class="py-14">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="clock" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                Derniers livres publiés
            </h2>

            <a href="{{ route('books.index') }}" class="text-sm font-medium text-[var(--faso-orange)] hover:underline inline-flex items-center gap-1">
                Voir tout <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
            @foreach(\App\Models\Book::where('status','published')->latest()->take(10)->get() as $book)
                @php
                    $isFree = ($book->access_type === 'free') || empty($book->price);
                    $priceLabel = $isFree ? 'Gratuit' : number_format($book->price, 0, ',', ' ') . ' FCFA';
                    $badgeText = strtoupper($book->format ?? 'PDF');
                @endphp

                <a href="{{ route('books.show', $book->slug) }}"
                   class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm
                          hover:shadow-xl hover:-translate-y-1 transition">

                    <div class="relative">
                        <img src="{{ asset('storage/'.$book->cover) }}"
                             loading="lazy"
                             class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">

                        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/90 text-slate-900
                                     text-[10px] font-semibold backdrop-blur border border-slate-200">
                            {{ $badgeText }}
                        </span>

                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold
                                         {{ $isFree ? 'bg-emerald-500 text-white' : 'bg-[var(--faso-orange)] text-white' }}
                                         shadow-lg shadow-black/10">
                                <i data-lucide="{{ $isFree ? 'gift' : 'wallet' }}" class="w-4 h-4"></i>
                                {{ $priceLabel }}
                            </span>

                            <span class="w-10 h-10 rounded-2xl bg-white/15 border border-white/25 backdrop-blur
                                         flex items-center justify-center text-white
                                         group-hover:bg-white/25 transition">
                                <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                            </span>
                        </div>
                    </div>

                    <div class="p-4 space-y-2">
                        <h3 class="font-extrabold text-[13px] sm:text-sm text-slate-900 leading-snug line-clamp-2">
                            {{ $book->title }}
                        </h3>

                        <p class="text-[11px] text-slate-500 flex items-center gap-1.5 truncate">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                        </p>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-[11px] text-slate-500 inline-flex items-center gap-1.5">
                                <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500"></i>
                                Recommandé
                            </span>
                            <span class="text-[11px] text-slate-500 inline-flex items-center gap-1.5">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[var(--faso-orange)]"></i>
                                Nouveau
                            </span>
                        </div>
                    </div>

                    <div class="absolute inset-0 rounded-3xl ring-0 ring-[var(--faso-orange)]/0 group-hover:ring-2 group-hover:ring-[var(--faso-orange)]/25 transition pointer-events-none"></div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     LIVRES GRATUITS — MÊME DESIGN (attirant)
============================================================ --}}
<section class="py-16 bg-gradient-to-r from-[var(--faso-green)]/7 via-white to-[var(--faso-orange)]/7">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">

        <div class="flex items-center justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="gift" class="w-6 h-6 text-[var(--faso-green)]"></i>
                Livres gratuits à découvrir
            </h2>

            <a href="{{ route('books.index') }}" class="text-sm font-medium text-[var(--faso-green)] hover:underline inline-flex items-center gap-1">
                Voir plus <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
            @foreach(\App\Models\Book::where('access_type','free')->where('status','published')->latest()->take(10)->get() as $book)

                <a href="{{ route('books.show', $book->slug) }}"
                   class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm
                          hover:shadow-xl hover:-translate-y-1 transition">

                    <div class="relative">
                        <img src="{{ asset('storage/'.$book->cover) }}"
                             loading="lazy"
                             class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">

                        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/90 text-slate-900
                                     text-[10px] font-semibold backdrop-blur border border-slate-200">
                            {{ strtoupper($book->format ?? 'PDF') }}
                        </span>

                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold
                                         bg-emerald-500 text-white shadow-lg shadow-black/10">
                                <i data-lucide="gift" class="w-4 h-4"></i>
                                Gratuit
                            </span>

                            <span class="w-10 h-10 rounded-2xl bg-white/15 border border-white/25 backdrop-blur
                                         flex items-center justify-center text-white
                                         group-hover:bg-white/25 transition">
                                <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                            </span>
                        </div>
                    </div>

                    <div class="p-4 space-y-2">
                        <h3 class="font-extrabold text-[13px] sm:text-sm text-slate-900 leading-snug line-clamp-2">
                            {{ $book->title }}
                        </h3>

                        <p class="text-[11px] text-slate-500 flex items-center gap-1.5 truncate">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                        </p>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-[11px] text-slate-500 inline-flex items-center gap-1.5">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[var(--faso-green)]"></i>
                                100% Free
                            </span>
                            <span class="text-[11px] text-slate-500 inline-flex items-center gap-1.5">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                Lecture facile
                            </span>
                        </div>
                    </div>

                    <div class="absolute inset-0 rounded-3xl ring-0 ring-emerald-500/0 group-hover:ring-2 group-hover:ring-emerald-500/20 transition pointer-events-none"></div>
                </a>

            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================
     CATÉGORIES POPULAIRES
============================================================ --}}
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">

        <div class="flex items-center justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="grid-3x3" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                Catégories populaires
            </h2>

            <a href="{{ route('categories.index.front') }}" class="text-sm font-medium text-[var(--faso-orange)] hover:underline inline-flex items-center gap-1">
                Voir toutes <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-6">
            @foreach(\App\Models\Category::take(6)->get() as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   class="group bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm hover:shadow-xl transition
                          hover:-translate-y-1 active:translate-y-0 hover:border-[var(--faso-orange)] flex flex-col items-center gap-2">

                    <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center group-hover:scale-105 transition">
                        <i data-lucide="folder-open" class="w-5 h-5 text-[var(--faso-orange)]"></i>
                    </div>

                    <p class="text-sm font-semibold text-slate-900 truncate max-w-[120px]">
                        {{ $cat->name }}
                    </p>
                    <p class="text-[11px] text-gray-500 line-clamp-2 max-w-[140px]">
                        {{ $cat->description }}
                    </p>
                </a>
            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================
     CTA AUTEURS — PREMIUM + PATTERN (CLEAN / RESPONSIVE)
============================================================ --}}
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">

        <div class="relative overflow-hidden rounded-3xl bg-[var(--faso-orange)] text-white shadow-2xl">

            {{-- Pattern SVG (discret) --}}
            <div class="absolute inset-0 opacity-[0.18] pointer-events-none">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="fasoPattern" width="56" height="56" patternUnits="userSpaceOnUse">
                            <path d="M0 28h56M28 0v56" stroke="white" stroke-opacity="0.18" stroke-width="1"/>
                            <circle cx="28" cy="28" r="2.2" fill="white" fill-opacity="0.22"/>
                            <path d="M14 14l28 28M42 14L14 42" stroke="white" stroke-opacity="0.10" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#fasoPattern)"/>
                </svg>
            </div>

            {{-- Glows soft --}}
            <div class="absolute -top-24 -left-24 w-72 h-72 bg-white/20 blur-3xl rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-28 -right-28 w-96 h-96 bg-white/15 blur-3xl rounded-full pointer-events-none"></div>

            <div class="relative p-7 sm:p-10 lg:p-12">
                <div class="grid lg:grid-cols-12 gap-8 items-center">

                    {{-- Left content --}}
                    <div class="lg:col-span-7 space-y-5">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/15 border border-white/20 text-[12px] font-semibold">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                            Espace auteurs & éditeurs
                        </div>

                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-[1.05] tracking-tight">
                            Publie ton manuscrit sur Fasolivre
                        </h2>

                        <p class="text-white/90 text-sm sm:text-base leading-relaxed max-w-2xl">
                            Donne vie à ton livre en numérique : mise en ligne, visibilité, lecteurs en Afrique & diaspora,
                            et monétisation simple.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                            <a href="{{ url('/submit') }}"
                               class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded-2xl
                                      bg-white text-[var(--faso-orange)] font-semibold
                                      shadow-lg shadow-black/10
                                      hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0">
                                <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                                Soumettre mon manuscrit
                            </a>

                            <a href="{{ route('authors.index.front') }}"
                               class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded-2xl
                                      bg-white/10 border border-white/25 text-white font-semibold
                                      hover:bg-white/15 hover:-translate-y-0.5 active:translate-y-0">
                                <i data-lucide="users" class="w-5 h-5"></i>
                                Voir les auteurs
                            </a>
                        </div>

                        <p class="text-[12px] text-white/80">
                            Accompagnement éditorial • Couverture & mise en page • Distribution • Paiements sécurisés
                        </p>
                    </div>

                    {{-- Right mini-card --}}
                    <div class="lg:col-span-5">
                        <div class="bg-white/12 backdrop-blur border border-white/20 rounded-3xl p-5 sm:p-6 shadow-lg">
                            <div class="flex items-center justify-between mb-4">
                                <p class="font-semibold">Ce que tu gagnes</p>
                                <span class="text-[11px] px-2 py-1 rounded-full bg-white/15 border border-white/20">
                                    Premium
                                </span>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center">
                                        <i data-lucide="eye" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Visibilité</p>
                                        <p class="text-white/85 text-[13px]">Ton livre accessible aux lecteurs partout.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center">
                                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Protection & contrôle</p>
                                        <p class="text-white/85 text-[13px]">Gestion simple et diffusion maîtrisée.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center">
                                        <i data-lucide="wallet" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Monétisation</p>
                                        <p class="text-white/85 text-[13px]">Vends ou propose gratuitement, au choix.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-white/20 flex items-center justify-between text-[12px] text-white/85">
                                <span class="inline-flex items-center gap-2">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    Réponse rapide
                                </span>
                                <span class="inline-flex items-center gap-2">
                                    <i data-lucide="globe-2" class="w-4 h-4"></i>
                                    Afrique & diaspora
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>

@endsection
