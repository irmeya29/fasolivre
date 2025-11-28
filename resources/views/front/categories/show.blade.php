@extends('front.layouts.app')

@section('title', $category->name . ' – Catégorie')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-semibold text-slate-900 flex items-center gap-2">
            <i data-lucide="folder" class="w-6 h-6 text-indigo-600"></i>
            {{ $category->name }}
        </h1>

        <a href="{{ route('categories.index.front') }}"
           class="text-sm text-slate-500 hover:text-indigo-600 flex items-center gap-1">
            <i data-lucide="arrow-left"></i> Retour
        </a>
    </div>

    {{-- Books --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">

        @foreach($books as $book)
            <a href="{{ route('books.show', $book->slug) }}"
               class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                <img src="{{ asset('storage/'.$book->cover) }}"
                     class="w-full h-56 object-cover">

                <div class="p-3">
                    <h3 class="font-medium text-sm">{{ Str::limit($book->title, 40) }}</h3>
                    <p class="text-xs text-gray-500">{{ $book->author->name }}</p>
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
