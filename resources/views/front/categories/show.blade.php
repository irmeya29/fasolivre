@extends('front.layouts.app')

@section('title', $category->name . ' – Catégorie')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
    }

    .glass {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .book-card:hover img {
        transform: scale(1.06);
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-12">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-10">

        <div>
            <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="folder" class="w-7 h-7 text-[var(--faso-orange)]"></i>
                {{ $category->name }}
            </h1>

            @if($category->description)
            <p class="text-sm text-slate-600 mt-1 max-w-2xl">
                {{ $category->description }}
            </p>
            @endif
        </div>

        <a href="{{ route('categories.index.front') }}"
           class="text-sm px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center gap-1 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </div>


    {{-- BOOK LIST --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8">

        @forelse($books as $book)

        <a href="{{ route('books.show', $book->slug) }}"
           class="glass rounded-2xl shadow-lg overflow-hidden transition book-card">

            <div class="relative">
                <img src="{{ asset('storage/'.$book->cover) }}"
                     class="w-full h-56 object-cover transition duration-300">

                {{-- Badge --}}
                @if($book->access_type === 'free')
                    <span class="absolute top-2 left-2 px-2 py-1 text-[10px] bg-green-100 text-[var(--faso-green)] rounded-lg">
                        GRATUIT
                    </span>
                @elseif($book->access_type === 'paid')
                    <span class="absolute top-2 left-2 px-2 py-1 text-[10px] bg-orange-100 text-[var(--faso-orange)] rounded-lg">
                        PAYANT
                    </span>
                @else
                    <span class="absolute top-2 left-2 px-2 py-1 text-[10px] bg-yellow-100 text-yellow-700 rounded-lg">
                        ABONNEMENT
                    </span>
                @endif
            </div>

            <div class="p-4">
                <h3 class="font-semibold text-sm text-slate-900 truncate">
                    {{ $book->title }}
                </h3>

                <p class="text-xs text-slate-500 mt-1 truncate flex items-center gap-1">
                    <i data-lucide="user" class="w-3 h-3"></i>
                    {{ $book->author->name ?? 'Auteur inconnu' }}
                </p>
            </div>

        </a>

        @empty
        <p class="text-slate-500 col-span-full text-center py-10">
            Aucun livre disponible dans cette catégorie.
        </p>
        @endforelse

    </div>


    {{-- PAGINATION --}}
    <div class="mt-12">
        {{ $books->links('pagination::tailwind') }}
    </div>

</div>

@endsection
