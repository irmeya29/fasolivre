@extends('front.layouts.app')

@section('title', 'Fasolivre – Lire les auteurs africains en numérique')

@section('content')

{{-- ============================================================
     HERO SECTION PREMIUM
============================================================ --}}
<section class="bg-gradient-to-b from-indigo-50/70 via-slate-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">

        {{-- LEFT SIDE --}}
        <div class="space-y-6">
            <p class="inline-flex items-center gap-2 rounded-full bg-white shadow-sm px-3 py-1 text-xs font-medium text-indigo-600 border border-indigo-100">
                <i data-lucide="sparkles" class="w-3 h-3 text-emerald-500"></i>
                Plateforme africaine de lecture
            </p>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-slate-900 leading-tight">
                Lis, découvre & soutiens
                <span class="text-indigo-600">les auteurs africains</span>
                en un clic.
            </h1>

            <p class="text-sm sm:text-base text-slate-600 max-w-xl">
                Fasolivre rassemble des e-books, romans, essais, BD et ouvrages académiques d’auteurs africains.
                Lis gratuitement, achète en toute sécurité, et soumets tes manuscrits pour publication.
            </p>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('books.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium bg-indigo-600 text-white shadow hover:bg-indigo-700">
                    <i data-lucide="library" class="w-4 h-4"></i>
                    Découvrir les livres
                </a>

                <a href="{{ url('/submit') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border border-slate-200 text-slate-700 hover:bg-slate-50">
                    <i data-lucide="file-plus" class="w-4 h-4"></i>
                    Publier mon manuscrit
                </a>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4 pt-4 text-xs text-slate-500">

                <div class="flex flex-col">
                    <span class="flex items-center gap-1 font-semibold text-slate-900 text-sm">
                        <i data-lucide="book-open" class="w-4 h-4 text-indigo-600"></i> +100
                    </span>
                    <p>e-books disponibles</p>
                </div>

                <div class="flex flex-col">
                    <span class="flex items-center gap-1 font-semibold text-slate-900 text-sm">
                        <i data-lucide="users" class="w-4 h-4 text-indigo-600"></i>Auteurs
                    </span>
                    <p>Afrique & diaspora</p>
                </div>

                <div class="flex flex-col">
                    <span class="flex items-center gap-1 font-semibold text-slate-900 text-sm">
                        <i data-lucide="smartphone" class="w-4 h-4 text-indigo-600"></i>Mobile
                    </span>
                    <p>App Flutter Fasolivre</p>
                </div>

            </div>
        </div>

        {{-- RIGHT SIDE (MOCKUP) --}}
        <div class="relative">
            <div class="absolute -top-6 -left-4 w-24 h-24 rounded-full bg-indigo-100 blur-2xl opacity-60"></div>
            <div class="absolute -bottom-8 -right-6 w-32 h-32 rounded-full bg-emerald-100 blur-3xl opacity-60"></div>

            <div class="relative bg-white border border-slate-100 shadow-xl rounded-3xl p-4 sm:p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 flex items-center gap-1">
                        <i data-lucide="star" class="w-4 h-4 text-yellow-500"></i>
                        En vedette
                    </span>

                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600">
                        Sélection automatique
                    </span>
                </div>

                {{-- ============================================================
                     LIVRES EN VEDETTE (AUTO) — 3 derniers publiés
                ============================================================ --}}
                <div class="grid grid-cols-3 gap-3 text-xs">
                    @foreach(\App\Models\Book::where('status','published')->latest()->take(3)->get() as $book)
                        <a href="{{ route('books.show', $book->slug) }}"
                           class="bg-slate-50 rounded-2xl p-2 flex flex-col gap-2 hover:shadow-md transition">

                            <img src="{{ asset('storage/'.$book->cover) }}"
                                 class="rounded-xl aspect-[3/4] object-cover">

                            <p class="font-semibold text-slate-900 truncate">
                                {{ Str::limit($book->title, 30) }}
                            </p>

                            <p class="text-[11px] text-slate-500 truncate flex items-center gap-1">
                                <i data-lucide="user" class="w-3 h-3"></i>
                                {{ $book->author->name ?? 'Auteur inconnu' }}
                            </p>
                        </a>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 pt-3 flex items-center justify-between text-xs">
                    <div class="flex flex-col">
                        <span class="text-slate-500">Disponible sur</span>
                        <span class="font-medium text-slate-900">Web & App mobile</span>
                    </div>

                    <div class="flex gap-2">
                        <span class="px-2 py-1 rounded-full bg-slate-100 text-[11px] flex items-center gap-1">
                            <i data-lucide="smartphone" class="w-3 h-3"></i> Android
                        </span>

                        <span class="px-2 py-1 rounded-full bg-slate-100 text-[11px] flex items-center gap-1">
                            <i data-lucide="tablet" class="w-3 h-3"></i> iOS
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>




{{-- ============================================================
     SECTION : DERNIERS LIVRES
============================================================ --}}
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="clock" class="w-5 h-5 text-indigo-600"></i>
                Derniers livres publiés
            </h2>

            <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                <i data-lucide="arrow-right"></i> Voir tout
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach(\App\Models\Book::where('status','published')->latest()->take(10)->get() as $book)
                <a href="{{ route('books.show', $book->slug) }}"
                   class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                    <img src="{{ asset('storage/'.$book->cover) }}" class="w-full h-56 object-cover">

                    <div class="p-3">
                        <h3 class="font-medium text-sm">{{ Str::limit($book->title, 40) }}</h3>
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <i data-lucide="user" class="w-3 h-3"></i>
                            {{ $book->author->name ?? '' }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>





{{-- ============================================================
     SECTION : LIVRES GRATUITS
============================================================ --}}
<section class="py-12 bg-indigo-50/50">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="gift" class="w-5 h-5 text-indigo-600"></i>
                Livres gratuits
            </h2>

            <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                <i data-lucide="arrow-right"></i> Voir tout
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach(\App\Models\Book::where('access_type','free')->take(10)->get() as $book)
                <a href="{{ route('books.show', $book->slug) }}"
                   class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                    <img src="{{ asset('storage/'.$book->cover) }}" class="w-full h-56 object-cover">

                    <div class="p-3">
                        <h3 class="font-medium text-sm">{{ $book->title }}</h3>
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <i data-lucide="user" class="w-3 h-3"></i>
                            {{ $book->author->name ?? '' }}
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
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-2">
                <i data-lucide="grid" class="w-5 h-5 text-indigo-600"></i>
                Catégories populaires
            </h2>

            <a href="{{ route('categories.index.front') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                <i data-lucide="arrow-right"></i> Voir tout
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach(\App\Models\Category::take(6)->get() as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   class="bg-white border shadow-sm rounded-xl p-4 text-center hover:shadow-lg transition">

                    <i data-lucide="folder" class="w-6 h-6 mx-auto text-indigo-600 mb-2"></i>

                    <p class="text-sm font-semibold text-slate-900">{{ $cat->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($cat->description, 40) }}</p>
                </a>
            @endforeach
        </div>

    </div>
</section>




{{-- ============================================================
     CTA AUTEURS
============================================================ --}}
<section class="py-20 bg-gradient-to-r from-indigo-600 to-indigo-800 text-white">
    <div class="max-w-4xl mx-auto px-4 text-center space-y-5">

        <h2 class="text-3xl sm:text-4xl font-semibold flex items-center justify-center gap-2">
            <i data-lucide="pen-tool" class="w-8 h-8 text-white"></i>
            Publie ton manuscrit sur Fasolivre
        </h2>

        <p class="max-w-xl mx-auto text-indigo-200 text-sm">
            Tu es auteur ? Tu veux publier ton premier roman, essai ou témoignage ?
            Envoie ton manuscrit et sois lu en Afrique et dans le monde entier.
        </p>

        <a href="{{ url('/submit') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-indigo-700 font-medium shadow hover:bg-gray-50">
            <i data-lucide="upload" class="w-5 h-5"></i>
            Soumettre mon manuscrit
        </a>

    </div>
</section>

@endsection
