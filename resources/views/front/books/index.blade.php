@extends('front.layouts.app')

@section('title', 'Tous les livres – Fasolivre')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
    }

    .glass {
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        background: rgba(255, 255, 255, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-12">

    {{-- HEADER SECTION --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-10">

        <div class="space-y-1">
            <h1 class="text-3xl font-extrabold text-slate-900 flex items-center gap-3">
                <i data-lucide="library" class="w-8 h-8 text-[var(--faso-orange)]"></i>
                Tous les livres
            </h1>
            <p class="text-sm text-slate-500">Explore l’ensemble de la bibliothèque Fasolivre</p>
        </div>

        {{-- SEARCH BAR --}}
        <form action="{{ route('search') }}" method="GET"
              class="glass rounded-2xl px-5 py-3 flex items-center gap-3 shadow-lg w-full lg:w-[380px] transition">

            <i data-lucide="search" class="w-5 h-5 text-slate-500"></i>

            <input type="text" name="q"
                   placeholder="Rechercher un livre, auteur..."
                   class="bg-transparent flex-1 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none">

            <button class="px-4 py-2 rounded-xl bg-gradient-to-r from-[var(--faso-orange)] to-[var(--faso-green)] text-white text-xs font-semibold shadow">
                Rechercher
            </button>
        </form>

    </div>


    {{-- BOOKS GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-7">

        @forelse($books as $book)
        <a href="{{ route('books.show', $book->slug) }}"
           class="group rounded-2xl bg-white/80 shadow-md hover:shadow-xl border border-slate-100 overflow-hidden transition flex flex-col backdrop-blur">

            {{-- COVER --}}
            <div class="relative">
                <img src="{{ asset('storage/'.$book->cover) }}"
                     class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">

                @if($book->access_type == 'free')
                    <span class="absolute top-3 left-3 px-2 py-1 rounded-lg bg-green-100 text-green-700 text-[10px] font-semibold">
                        GRATUIT
                    </span>
                @elseif($book->access_type == 'paid')
                    <span class="absolute top-3 left-3 px-2 py-1 rounded-lg bg-orange-100 text-[var(--faso-orange)] text-[10px] font-semibold">
                        PAYANT
                    </span>
                @else
                    <span class="absolute top-3 left-3 px-2 py-1 rounded-lg bg-indigo-100 text-indigo-700 text-[10px] font-semibold">
                        ABONNEMENT
                    </span>
                @endif
            </div>

            {{-- DETAILS --}}
            <div class="p-4 flex flex-col flex-1">

                <h3 class="font-semibold text-sm text-slate-900 leading-tight mb-1 truncate">
                    {{ $book->title }}
                </h3>

                <p class="text-[11px] text-slate-500 flex items-center gap-1 truncate">
                    <i data-lucide="user" class="w-3 h-3"></i>
                    {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                </p>

                {{-- PRICE --}}
                <div class="mt-auto pt-3">
                    @if($book->access_type == 'free')
                        <span class="text-xs px-3 py-1 rounded-lg bg-green-100 text-green-700 font-medium">
                            Gratuit
                        </span>

                    @elseif($book->access_type == 'paid')
                        <span class="text-xs px-3 py-1 rounded-lg bg-[var(--faso-orange)]/10 text-[var(--faso-orange)] font-semibold">
                            {{ number_format($book->price, 0, ',', ' ') }} FCFA
                        </span>

                    @else
                        <span class="text-xs px-3 py-1 rounded-lg bg-indigo-100 text-indigo-700 font-semibold">
                            Abonnement
                        </span>
                    @endif
                </div>

            </div>

        </a>
        @empty

            <div class="col-span-full text-center text-slate-500 py-20">
                Aucun livre trouvé pour le moment.
            </div>

        @endforelse

    </div>


    {{-- PAGINATION --}}
    <div class="mt-12 flex justify-center">
        {{ $books->links('pagination::tailwind') }}
    </div>

</div>

@endsection
