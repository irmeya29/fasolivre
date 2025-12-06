@extends('front.layouts.app')

@section('title', $author->name . ' – Auteur africain')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
    }

    .glass {
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
</style>

<div class="max-w-6xl mx-auto px-4 py-12">

    {{-- BREADCRUMB --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-8">
        <a href="{{ route('authors.index.front') }}" class="hover:text-[var(--faso-orange)] flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour aux auteurs
        </a>
    </div>


    {{-- AUTHOR CARD --}}
    <div class="glass rounded-3xl shadow-xl p-10 flex flex-col lg:flex-row gap-10 border border-white/50">

        {{-- PHOTO --}}
        <div class="relative">
            <div class="absolute inset-0 w-40 h-40 bg-[var(--faso-orange)]/20 blur-2xl rounded-full"></div>

            <img src="{{ $author->photo ? asset('storage/'.$author->photo) : 'https://ui-avatars.com/api/?name='.urlencode($author->name).'&size=256' }}"
                 class="relative w-40 h-40 rounded-2xl object-cover shadow-xl border border-white/50">
        </div>

        {{-- INFO --}}
        <div class="flex-1 space-y-5">

            <h1 class="text-4xl font-bold text-slate-900">
                {{ $author->name }}
            </h1>

            {{-- BIO --}}
            <p class="text-slate-700 text-sm leading-relaxed">
                {!! nl2br(e($author->bio)) !!}
            </p>

            {{-- SOCIAL LINKS --}}
            <div class="flex flex-wrap items-center gap-5 text-sm">

                @if($author->website)
                <a href="{{ $author->website }}" target="_blank"
                   class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-[var(--faso-orange)] transition">
                    <i data-lucide="globe" class="w-4 h-4"></i> Site web
                </a>
                @endif

                @if($author->facebook)
                <a href="{{ $author->facebook }}" target="_blank"
                   class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-[var(--faso-orange)] transition">
                    <i data-lucide="facebook" class="w-4 h-4"></i> Facebook
                </a>
                @endif

                @if($author->instagram)
                <a href="{{ $author->instagram }}" target="_blank"
                   class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-[var(--faso-orange)] transition">
                    <i data-lucide="instagram" class="w-4 h-4"></i> Instagram
                </a>
                @endif

            </div>
        </div>

    </div>


    {{-- BOOKS BY AUTHOR --}}
    <div class="mt-16">
        <h2 class="text-xl font-semibold text-slate-900 mb-6 flex items-center gap-2">
            <i data-lucide="book-open" class="w-5 h-5 text-[var(--faso-orange)]"></i>
            Livres de {{ $author->name }}
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">

            @forelse($author->books as $book)
            <a href="{{ route('books.show', $book->slug) }}"
               class="rounded-2xl bg-white shadow hover:shadow-xl transition overflow-hidden group">

                <div class="relative">
                    <img src="{{ asset('storage/'.$book->cover) }}"
                         class="w-full h-56 object-cover group-hover:scale-105 transition duration-300">

                    @if($book->access_type === 'free')
                    <span class="absolute top-2 left-2 px-2 py-1 text-[10px] bg-green-100 text-[var(--faso-green)] rounded-lg">
                        GRATUIT
                    </span>
                    @elseif($book->access_type === 'paid')
                    <span class="absolute top-2 left-2 px-2 py-1 text-[10px] bg-orange-100 text-[var(--faso-orange)] rounded-lg">
                        PAYANT
                    </span>
                    @endif
                </div>

                <div class="p-3">
                    <h3 class="font-medium text-sm truncate">{{ $book->title }}</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ number_format($book->price, 0, ',', ' ') }} FCFA
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
