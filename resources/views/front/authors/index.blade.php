@extends('front.layouts.app')

@section('title', 'Auteurs africains – Fasolivre')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
    }

    .glass {
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .author-hover:hover {
        transform: translateY(-4px);
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-12">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 flex items-center gap-3">
                <i data-lucide="users" class="w-8 h-8 text-[var(--faso-orange)]"></i>
                Tous les auteurs
            </h1>
            <p class="text-sm text-slate-500 mt-1">Découvre les voix littéraires de l’Afrique & de la diaspora</p>
        </div>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8">

        @foreach($authors as $author)
        <a href="{{ route('authors.show', $author->slug) }}"
           class="glass rounded-3xl p-5 shadow-lg hover:shadow-2xl transition author-hover text-center border border-white/40">

            {{-- Photo --}}
            <div class="relative mx-auto">
                <div class="absolute inset-0 w-24 h-24 bg-[var(--faso-orange)]/20 blur-xl rounded-full mx-auto"></div>
                <img src="{{ $author->photo ? asset('storage/'.$author->photo) : 'https://ui-avatars.com/api/?name='.urlencode($author->name).'&size=256' }}"
                     class="relative w-24 h-24 mx-auto rounded-full object-cover shadow-lg border border-white">
            </div>

            {{-- Name --}}
            <h3 class="mt-4 font-semibold text-slate-900 text-sm truncate">
                {{ $author->name }}
            </h3>

            {{-- Bio --}}
            <p class="text-[11px] text-slate-500 mt-1 leading-snug">
                {{ Str::limit($author->bio, 50) }}
            </p>

            {{-- Count --}}
            <div class="mt-3 text-xs inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gradient-to-r from-[var(--faso-orange)]/15 to-[var(--faso-green)]/15 text-[var(--faso-orange)] font-medium">
                <i data-lucide="book-open" class="w-3 h-3"></i>
                {{ $author->books()->count() }} livres
            </div>

        </a>
        @endforeach

    </div>

    {{-- Pagination --}}
    <div class="mt-12">
        {{ $authors->links('pagination::tailwind') }}
    </div>

</div>

@endsection
