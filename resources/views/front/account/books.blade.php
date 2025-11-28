@extends('front.layouts.app')

@section('title', 'Mes livres')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-12">

    <h1 class="text-2xl font-semibold text-slate-900 mb-8 flex items-center gap-3">
        <i data-lucide="book-open" class="w-7 h-7 text-indigo-600"></i>
        Mes livres
    </h1>

    @if($books->isEmpty())
        <div class="bg-white shadow rounded-2xl p-8 text-center">
            <p class="text-slate-500 text-sm mb-4">Vous n’avez encore aucun livre.</p>
            <a href="{{ route('books.index') }}"
               class="px-6 py-3 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700 inline-flex items-center gap-2">
               <i data-lucide="book"></i> Explorer les livres
            </a>
        </div>
    @else

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">

            @foreach($books as $book)
            <div class="bg-white shadow rounded-xl overflow-hidden">

                <img src="{{ asset('storage/'.$book->cover) }}"
                     class="w-full h-52 object-cover">

                <div class="p-4 space-y-2">

                    <h3 class="font-semibold text-slate-900 text-sm truncate">
                        {{ $book->title }}
                    </h3>

                    <p class="text-xs text-slate-500 truncate">
                        {{ $book->author->name }}
                    </p>

                    {{-- TYPE --}}
                    @if($book->access_type === 'free')
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">
                            Gratuit
                        </span>
                    @else
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">
                            {{ number_format($book->price, 0) }} FCFA
                        </span>
                    @endif

                    {{-- ACTION --}}
                    <div class="pt-2">
                        <a href="{{ route('read.book', $book->slug) }}"
                           class="inline-flex items-center gap-1 px-3 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 w-full justify-center">
                            <i data-lucide="book-open"></i>
                            Lire
                        </a>
                    </div>

                </div>

            </div>
            @endforeach

        </div>
    @endif

</div>

@endsection
