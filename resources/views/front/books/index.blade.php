@extends('front.layouts.app')

@section('title', 'Tous les livres – Fasolivre')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-semibold text-slate-900 flex items-center gap-2">
            <i data-lucide="library" class="w-6 h-6 text-indigo-600"></i>
            Tous les livres
        </h1>

        {{-- Search --}}
        <form action="{{ route('search') }}" method="GET" class="flex items-center gap-2">
            <input type="text" name="q"
                   placeholder="Rechercher un livre..."
                   class="w-64 px-4 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-400">
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-xl flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                Rechercher
            </button>
        </form>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">

        @foreach($books as $book)
        <a href="{{ route('books.show', $book->slug) }}"
           class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

            <img src="{{ asset('storage/'.$book->cover) }}"
                 class="w-full h-56 object-cover">

            <div class="p-3">
                <h3 class="font-medium text-sm">{{ Str::limit($book->title, 40) }}</h3>
                <p class="text-xs text-gray-500 flex items-center gap-1">
                    <i data-lucide="user" class="w-3 h-3"></i>
                    {{ $book->author->name ?? '' }}
                </p>

                {{-- Price --}}
                <div class="mt-2">
                    @if($book->access_type == 'free')
                        <span class="text-xs px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700">Gratuit</span>
                    @elseif($book->access_type == 'paid')
                        <span class="text-xs px-2 py-1 rounded-lg bg-indigo-100 text-indigo-700">
                            {{ number_format($book->price, 0) }} FCFA
                        </span>
                    @else
                        <span class="text-xs px-2 py-1 rounded-lg bg-orange-100 text-orange-700">Abonnement</span>
                    @endif
                </div>

            </div>
        </a>
        @endforeach

    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $books->links('pagination::tailwind') }}
    </div>

</div>

@endsection
