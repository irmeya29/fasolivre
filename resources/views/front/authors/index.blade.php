@extends('front.layouts.app')

@section('title', 'Auteurs africains – Fasolivre')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-semibold text-slate-900 flex items-center gap-2">
            <i data-lucide="users" class="w-6 h-6 text-indigo-600"></i>
            Tous les auteurs
        </h1>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8">

        @foreach($authors as $author)
        <a href="{{ route('authors.show', $author->slug) }}"
           class="bg-white rounded-2xl shadow hover:shadow-lg transition p-4 text-center">

            {{-- Photo --}}
            <img src="{{ $author->photo ? asset('storage/'.$author->photo) : 'https://ui-avatars.com/api/?name='.urlencode($author->name) }}"
                 class="w-24 h-24 mx-auto rounded-full object-cover shadow">

            <h3 class="mt-4 font-semibold text-slate-900 text-sm">{{ $author->name }}</h3>

            <p class="text-xs text-slate-500 mt-1">
                {{ Str::limit($author->bio, 40) }}
            </p>

            <div class="mt-3 text-xs text-indigo-600">
                <i data-lucide="book-open" class="w-3 h-3 inline"></i>
                {{ $author->books()->count() }} livres
            </div>

        </a>
        @endforeach

    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $authors->links('pagination::tailwind') }}
    </div>

</div>

@endsection
