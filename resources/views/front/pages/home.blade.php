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

    .floating {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%   { transform: translateY(0px); }
        50%  { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
</style>


{{-- ============================================================
     HERO SECTION — NEO GLASS PREMIUM
============================================================ --}}
<section class="relative overflow-hidden bg-gradient-to-b from-white via-[#fff7f2] to-[var(--faso-orange)]/10 pt-16 pb-20 lg:pt-24 lg:pb-24">

    {{-- Glow background --}}
    <div class="absolute inset-0 pointer-events-none -z-10">
        <div class="absolute -top-10 -left-10 w-64 h-64 bg-[var(--faso-orange)]/25 blur-3xl rounded-full"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-[var(--faso-green)]/25 blur-3xl rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 lg:px-6 grid lg:grid-cols-2 gap-12 items-center">

        {{-- LEFT --}}
        <div class="space-y-7">

            <div class="inline-flex items-center gap-2 bg-white/80 border border-orange-200/70 text-[var(--faso-orange)] shadow-md px-4 py-1.5 rounded-full text-[11px] font-semibold backdrop-blur">
                <i data-lucide="sparkles" class="w-3 h-3 text-[var(--faso-green)]"></i>
                Plateforme africaine de lecture numérique
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-tight">
                La bibliothèque
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-[var(--faso-orange)] to-[var(--faso-green)]">
                    100% africaine
                </span>
                à portée de main.
            </h1>

            <p class="text-sm sm:text-base lg:text-lg text-slate-600 max-w-xl leading-relaxed">
                Romans, essais, poésie, BD, ouvrages académiques… Fasolivre rassemble les voix d’auteurs d’Afrique et de la diaspora, dans une expérience de lecture moderne et accessible.
            </p>

            {{-- ACTIONS --}}
            <div class="flex flex-wrap items-center gap-4 pt-2">

                <a href="{{ route('books.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm font-semibold text-white
                          bg-gradient-to-r from-[var(--faso-orange)] to-[var(--faso-green)]
                          shadow-lg shadow-[var(--faso-orange)]/30 hover:shadow-xl hover:translate-y-0.5">
                    <i data-lucide="library" class="w-5 h-5"></i>
                    Explorer les livres
                </a>

                <a href="{{ url('/submit') }}"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-sm font-semibold
                          border border-slate-200 text-slate-800 bg-white/80 hover:bg-slate-50 shadow-sm">
                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                    Publier mon manuscrit
                </a>
            </div>

            {{-- QUICK INFO --}}
            <div class="grid grid-cols-3 gap-4 pt-6 text-xs text-slate-500">

                <div class="flex flex-col gap-1">
                    <span class="flex items-center gap-2 font-semibold text-slate-900 text-sm">
                        <i data-lucide="book-open" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                        +100
                    </span>
                    <p>Livres numériques disponibles</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="flex items-center gap-2 font-semibold text-slate-900 text-sm">
                        <i data-lucide="users" class="w-4 h-4 text-[var(--faso-green)]"></i>
                        Auteurs
                    </span>
                    <p>Afrique & diaspora</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="flex items-center gap-2 font-semibold text-slate-900 text-sm">
                        <i data-lucide="smartphone" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                        Bientôt
                    </span>
                    <p>App mobile Fasolivre</p>
                </div>

            </div>

        </div>

        {{-- RIGHT — NEO GLASS BOOKS PREVIEW --}}
        <div class="relative floating">

            {{-- Glow --}}
            <div class="absolute -top-6 -left-6 w-28 h-28 bg-white/60 blur-3xl rounded-full"></div>
            <div class="absolute bottom-0 -right-8 w-40 h-40 bg-[var(--faso-green)]/30 blur-3xl rounded-full"></div>

            <div class="relative space-y-4">

                {{-- Carte principale glass --}}
                <div class="bg-white/80 backdrop-blur-xl border border-white/70 shadow-2xl rounded-3xl p-5 sm:p-6 space-y-4">

                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                            <i data-lucide="flame" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                            En vedette
                        </div>

                        <span class="text-[11px] px-2 py-1 rounded-full bg-orange-50 text-[var(--faso-orange)] font-medium">
                            Dernières parutions
                        </span>
                    </div>

                    {{-- Livres en vedette --}}
                    <div class="grid grid-cols-3 gap-3 text-xs">
                        @foreach(\App\Models\Book::where('status','published')->latest()->take(3)->get() as $book)
                            <a href="{{ route('books.show', $book->slug) }}"
                               class="group bg-slate-50/80 hover:bg-white rounded-2xl p-2 flex flex-col gap-2 shadow-sm hover:shadow-md transition">

                                <div class="relative">
                                    <img src="{{ asset('storage/'.$book->cover) }}"
                                         class="rounded-xl aspect-[3/4] object-cover w-full group-hover:scale-105 transition">
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

                    {{-- Bas de carte --}}
                    <div class="border-t border-slate-100 pt-3 flex items-center justify-between text-[11px] text-slate-500">
                        <div>
                            <p class="font-medium text-slate-800">Lecture en ligne & téléchargement</p>
                            <p class="text-[10px]">Accède à tes livres partout, à tout moment.</p>
                        </div>

                        <div class="flex flex-col items-end gap-1">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100">
                                <i data-lucide="shield-check" class="w-3 h-3 text-[var(--faso-green)]"></i>
                                Paiements sécurisés
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100">
                                <i data-lucide="globe-2" class="w-3 h-3 text-[var(--faso-orange)]"></i>
                                Afrique & diaspora
                            </span>
                        </div>
                    </div>

                </div>

                {{-- Bandeau sous la carte --}}
                <div class="hidden sm:flex items-center gap-3 bg-white/80 backdrop-blur-xl border border-white/60 rounded-2xl px-4 py-3 shadow-lg">
                    <i data-lucide="sparkles" class="w-4 h-4 text-[var(--faso-green)]"></i>
                    <p class="text-[11px] text-slate-600">
                        Rejoins Fasolivre dès maintenant et sois parmi les premiers à profiter de la nouvelle expérience de lecture africaine.
                    </p>
                </div>

            </div>
        </div>

    </div>
</section>



{{-- ============================================================
     SECTION : BARRE D’ACCÈS RAPIDE (chips)
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
                Auteurs africains
            </a>
        </div>
    </div>
</section>



{{-- ============================================================
     SECTION : DERNIERS LIVRES
============================================================ --}}
<section class="py-14">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="clock" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                Derniers livres publiés
            </h2>

            <a href="{{ route('books.index') }}" class="text-sm font-medium text-[var(--faso-orange)] hover:underline flex items-center gap-1">
                Voir tout <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-7">
            @foreach(\App\Models\Book::where('status','published')->latest()->take(10)->get() as $book)
                <a href="{{ route('books.show', $book->slug) }}"
                   class="group bg-white/90 backdrop-blur border border-slate-100 rounded-2xl shadow-md hover:shadow-xl overflow-hidden transition flex flex-col">

                    <div class="relative">
                        <img src="{{ asset('storage/'.$book->cover) }}"
                             class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-3 right-3 px-2 py-1 rounded-full bg-white/80 text-[var(--faso-green)]
                                     text-[10px] font-semibold backdrop-blur">
                            Nouveau
                        </span>
                    </div>

                    <div class="p-3 flex-1 flex flex-col">
                        <h3 class="font-semibold text-sm text-slate-900 truncate mb-1">
                            {{ $book->title }}
                        </h3>
                        <p class="text-[11px] text-gray-500 truncate flex items-center gap-1 mb-2">
                            <i data-lucide="user" class="w-3 h-3"></i>
                            {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                        </p>
                        @if($book->price && $book->access_type !== 'free')
                            <span class="text-[11px] font-semibold text-[var(--faso-orange)] mt-auto">
                                {{ number_format($book->price, 0, ',', ' ') }} FCFA
                            </span>
                        @else
                            <span class="text-[11px] font-semibold text-[var(--faso-green)] mt-auto">
                                Gratuit
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</section>



{{-- ============================================================
     SECTION : LIVRES GRATUITS
============================================================ --}}
<section class="py-16 bg-gradient-to-r from-[var(--faso-green)]/8 via-white to-[var(--faso-orange)]/8">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="gift" class="w-6 h-6 text-[var(--faso-green)]"></i>
                Livres gratuits à découvrir
            </h2>

            <a href="{{ route('books.index') }}" class="text-sm font-medium text-[var(--faso-green)] hover:underline flex items-center gap-1">
                Voir plus <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-7">
            @foreach(\App\Models\Book::where('access_type','free')->where('status','published')->take(10)->get() as $book)
                <a href="{{ route('books.show', $book->slug) }}"
                   class="group bg-white/90 backdrop-blur border border-slate-100 rounded-2xl shadow-md hover:shadow-xl overflow-hidden transition flex flex-col">

                    <div class="relative">
                        <img src="{{ asset('storage/'.$book->cover) }}"
                             class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">

                        <span class="absolute top-3 left-3 px-2 py-1 rounded-full bg-green-100 text-[var(--faso-green)]
                                     text-[10px] font-semibold">
                            GRATUIT
                        </span>
                    </div>

                    <div class="p-3 flex-1 flex flex-col">
                        <h3 class="font-semibold text-sm text-slate-900 truncate mb-1">
                            {{ $book->title }}
                        </h3>
                        <p class="text-[11px] text-gray-500 truncate flex items-center gap-1 mt-auto">
                            <i data-lucide="user" class="w-3 h-3"></i>
                            {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</section>



{{-- ============================================================
     SECTION : CATÉGORIES POPULAIRES
============================================================ --}}
<section class="py-18 py-16">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="grid-3x3" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                Catégories populaires
            </h2>

            <a href="{{ route('categories.index.front') }}" class="text-sm font-medium text-[var(--faso-orange)] hover:underline flex items-center gap-1">
                Voir toutes les catégories <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach(\App\Models\Category::take(6)->get() as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   class="bg-white/90 backdrop-blur border border-slate-200 rounded-2xl p-5 text-center hover:shadow-xl hover:border-[var(--faso-orange)] transition flex flex-col items-center gap-2">

                    <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center">
                        <i data-lucide="folder-open" class="w-5 h-5 text-[var(--faso-orange)]"></i>
                    </div>

                    <p class="text-sm font-semibold text-slate-900 truncate max-w-[120px]">
                        {{ $cat->name }}
                    </p>
                    <p class="text-[11px] text-gray-500 truncate max-w-[130px]">
                        {{ $cat->description }}
                    </p>
                </a>
            @endforeach
        </div>

    </div>
</section>



{{-- ============================================================
     CTA AUTEURS — NEO GLASS
============================================================ --}}
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 lg:px-6">

        <div class="bg-gradient-to-r from-[var(--faso-orange)] to-[var(--faso-green)]
                    rounded-3xl p-8 sm:p-10 text-white shadow-2xl relative overflow-hidden">

            <div class="absolute inset-0 opacity-20 pointer-events-none"
                 style="background-image: radial-gradient(circle at 10% 20%, #ffffff 0, transparent 50%), radial-gradient(circle at 80% 0, #ffffff 0, transparent 55%);">
            </div>

            <div class="relative space-y-5 text-center sm:text-left">
                <h2 class="text-3xl sm:text-4xl font-extrabold flex items-center gap-3 justify-center sm:justify-start">
                    <i data-lucide="pen-tool" class="w-8 h-8"></i>
                    Publie ton manuscrit sur Fasolivre
                </h2>

                <p class="text-sm sm:text-base text-orange-50 max-w-xl">
                    Tu es auteur, éditeur, ou tu prépares ton premier livre ?
                    Fais-le vivre en numérique, et donne-lui des lecteurs en Afrique et dans le monde entier avec Fasolivre.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 items-center sm:items-start sm:justify-start justify-center">
                    <a href="{{ url('/submit') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-[var(--faso-orange)] font-semibold shadow-md hover:bg-orange-50">
                        <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                        Soumettre mon manuscrit
                    </a>

                    <span class="text-[11px] text-orange-100">
                        Accompagnement éditorial, mise en ligne, visibilité et gestion de la monétisation.
                    </span>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
