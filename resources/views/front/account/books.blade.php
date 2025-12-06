@extends('front.layouts.app')

@section('title', 'Mes livres – Fasolivre')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">


    {{-- ===========================================
         TITRE PAGE
    ============================================ --}}
    <h1 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-2">
        <i data-lucide="book-open" class="w-6 h-6 text-[#E0551B]"></i>
        Mes livres
    </h1>


    {{-- ===========================================
         SI L'UTILISATEUR N’A AUCUN LIVRE
    ============================================ --}}
    @if(auth()->user()->books()->count() == 0)

        <div class="bg-white p-10 rounded-2xl border shadow-sm text-center">
            <i data-lucide="alert-circle" class="w-12 h-12 text-slate-400 mx-auto mb-4"></i>

            <h2 class="text-xl font-semibold text-slate-800 mb-2">
                Aucun livre pour le moment
            </h2>

            <p class="text-slate-500 text-sm mb-6">
                Explorez notre bibliothèque et commencez votre aventure littéraire.
            </p>

            <a href="{{ route('books.index') }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-white bg-gradient-to-r
                      from-[#E0551B] to-[#079C25] hover:opacity-90 transition font-medium">
                <i data-lucide="library"></i> Voir les livres
            </a>
        </div>

    @else


    {{-- ===========================================
         LISTE DES LIVRES
    ============================================ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">

        @foreach(auth()->user()->books as $book)

            <div class="bg-white rounded-xl border shadow-sm hover:shadow-xl transition overflow-hidden">

                {{-- Couverture --}}
                <a href="{{ route('books.show', $book->slug) }}">
                    <img src="{{ asset('storage/'.$book->cover) }}"
                         class="w-full h-56 object-cover hover:scale-105 transition duration-300">
                </a>

                <div class="p-4 space-y-2">

                    {{-- Titre --}}
                    <h3 class="font-semibold text-slate-900 text-sm leading-tight">
                        {{ Str::limit($book->title, 40) }}
                    </h3>

                    {{-- Auteur --}}
                    <p class="text-xs text-slate-500 flex items-center gap-1">
                        <i data-lucide="user" class="w-3 h-3"></i>
                        {{ $book->author->name ?? 'Auteur inconnu' }}
                    </p>


                    {{-- Badge accès --}}
                    @if($book->access_type == 'free')
                        <span class="inline-block text-[11px] px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full">
                            Gratuit
                        </span>

                    @elseif($book->access_type == 'paid')
                        <span class="inline-block text-[11px] px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full">
                            {{ number_format($book->price, 0) }} FCFA
                        </span>

                    @else
                        <span class="inline-block text-[11px] px-2 py-1 bg-orange-100 text-orange-700 rounded-full">
                            Abonnement
                        </span>
                    @endif


                    {{-- BOUTON LECTURE --}}
                    <a href="{{ route('read.book', $book->slug) }}"
                       class="block mt-3 w-full text-center px-3 py-2 rounded-lg text-sm font-medium
                              text-white bg-[#079C25] hover:bg-[#06801f] transition">
                        Lire maintenant
                    </a>

                </div>

            </div>

        @endforeach

    </div>

    @endif

</div>

@endsection
