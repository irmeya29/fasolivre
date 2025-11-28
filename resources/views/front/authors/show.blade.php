@extends('front.layouts.app')

@section('title', $author->name . ' – Auteur africain')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-10">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('authors.index.front') }}" class="hover:text-indigo-600 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </div>


    {{-- HEADER --}}
    <div class="bg-white rounded-3xl shadow p-8 flex flex-col lg:flex-row gap-10">

        {{-- Photo --}}
        <img src="{{ $author->photo ? asset('storage/'.$author->photo) : 'https://ui-avatars.com/api/?name='.urlencode($author->name) }}"
             class="w-40 h-40 rounded-2xl object-cover shadow">

        {{-- Infos --}}
        <div class="flex-1 space-y-4">

            <h1 class="text-3xl font-semibold text-slate-900">
                {{ $author->name }}
            </h1>

            <p class="text-slate-600 text-sm leading-relaxed">
                {!! nl2br(e($author->bio)) !!}
            </p>

            {{-- Socials --}}
            <div class="flex items-center gap-4 text-sm text-slate-500">
                @if($author->website)
                <a href="{{ $author->website }}" target="_blank" class="hover:text-indigo-600 flex items-center gap-1">
                    <i data-lucide="globe" class="w-4 h-4"></i> Site web
                </a>
                @endif

                @if($author->facebook)
                <a href="{{ $author->facebook }}" target="_blank" class="hover:text-indigo-600 flex items-center gap-1">
                    <i data-lucide="facebook" class="w-4 h-4"></i> Facebook
                </a>
                @endif

                @if($author->instagram)
                <a href="{{ $author->instagram }}" target="_blank" class="hover:text-indigo-600 flex items-center gap-1">
                    <i data-lucide="instagram" class="w-4 h-4"></i> Instagram
                </a>
                @endif
            </div>

        </div>

    </div>


    {{-- BOOKS SECTION --}}
    <div class="mt-16">
        <h2 class="text-xl font-semibold text-slate-900 mb-6 flex items-center gap-2">
            <i data-lucide="book-open" class="w-5 h-5 text-indigo-600"></i>
            Livres de {{ $author->name }}
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">

            @forelse($author->books as $book)
            <a href="{{ route('books.show', $book->slug) }}"
               class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                <img src="{{ asset('storage/'.$book->cover) }}"
                     class="w-full h-56 object-cover">

                <div class="p-3">
                    <h3 class="font-medium text-sm">{{ Str::limit($book->title, 40) }}</h3>
                    <p class="text-xs text-gray-500">
                        {{ number_format($book->price, 0) }} FCFA
                    </p>
                </div>

            </a>
            @empty
                <p class="text-slate-500 text-sm">Aucun livre publié pour le moment.</p>
            @endforelse

        </div>
    </div>

</div>

@endsection
