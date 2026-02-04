@extends('front.layouts.app')

@section('title', 'Auteurs africains – Fasolivre')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
    }

    .soft-card{
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        box-shadow: 0 10px 25px rgba(2,6,23,.06);
    }

    .clamp-2{
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Pagination élégante + ellipsis */
    .pg a, .pg span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 13px;
        border: 1px solid rgb(226 232 240);
        background: #fff;
        color: rgb(51 65 85);
        transition: all .2s ease;
        user-select: none;
    }
    .pg a:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 25px rgba(2,6,23,.08);
        border-color: rgba(224,85,27,.25);
        color: var(--faso-orange);
    }
    .pg .active span {
        background: var(--faso-orange);
        color: #fff;
        border-color: rgba(224,85,27,.35);
        box-shadow: 0 10px 25px rgba(224,85,27,.22);
    }
    .pg .disabled span {
        opacity: .45;
        cursor: not-allowed;
    }
    .pg .dots span{
        border-style: dashed;
        opacity: .7;
    }
</style>

@php
    // Pour la recherche
    $search = request('q', '');
@endphp

<div class="max-w-7xl mx-auto px-4 py-10 lg:py-12">

    {{-- HEADER --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-b from-white to-[#fff7f2] p-6 sm:p-8 mb-8">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-[var(--faso-orange)]/10 blur-3xl rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-[var(--faso-green)]/10 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-[11px] font-semibold text-slate-700">
                    <i data-lucide="sparkles" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                    Communauté
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 flex items-center gap-3">
                    <i data-lucide="users" class="w-9 h-9 text-[var(--faso-orange)]"></i>
                    Auteurs africains
                </h1>

                <p class="text-sm text-slate-600">
                    Découvre les voix de l’Afrique & de la diaspora
                    <span class="text-slate-400">•</span>
                    <span class="font-semibold text-slate-800">{{ $authors->total() }}</span> auteurs
                </p>
            </div>

            {{-- Recherche --}}
            <form action="{{ route('authors.index.front') }}" method="GET"
                  class="w-full lg:w-[520px] rounded-2xl p-2 flex items-center gap-2 soft-card">
                <div class="flex items-center gap-2 px-3">
                    <i data-lucide="search" class="w-5 h-5 text-slate-500"></i>
                </div>

                <input type="text" name="q" value="{{ $search }}"
                       placeholder="Rechercher un auteur..."
                       class="bg-transparent flex-1 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none px-1">

                @if($search)
                    <a href="{{ route('authors.index.front') }}"
                       class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-extrabold hover:bg-slate-200">
                        Réinitialiser
                    </a>
                @endif

                <button class="px-4 py-2.5 rounded-xl bg-[var(--faso-orange)] text-white text-xs font-extrabold
                               shadow hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                    Rechercher
                </button>
            </form>
        </div>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
        @forelse($authors as $author)
            @php
                $avatar = $author->photo
                    ? asset('storage/'.$author->photo)
                    : 'https://ui-avatars.com/api/?name='.urlencode($author->name).'&size=256&background=E0551B&color=fff';

                $count = (int) ($author->books_count ?? 0);
                $bio = $author->bio ? \Illuminate\Support\Str::limit($author->bio, 90) : 'Auteur présent sur Fasolivre.';
            @endphp

            <a href="{{ route('authors.show', $author->slug) }}"
               class="group soft-card rounded-3xl p-5 hover:shadow-xl hover:-translate-y-1 transition">

                {{-- top row --}}
                <div class="flex items-start gap-3">
                    <div class="relative">
                        <div class="absolute -inset-2 bg-[var(--faso-orange)]/10 blur-xl rounded-2xl"></div>
                        <img src="{{ $avatar }}"
                             alt="{{ $author->name }}"
                             class="relative w-14 h-14 rounded-2xl object-cover border border-white shadow-sm">
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="font-extrabold text-slate-900 text-sm truncate group-hover:text-[var(--faso-orange)] transition">
                            {{ $author->name }}
                        </h3>

                        <div class="mt-1 inline-flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600">
                            <i data-lucide="book-open" class="w-4 h-4 text-[var(--faso-green)]"></i>
                            {{ $count }} {{ $count > 1 ? 'livres' : 'livre' }}
                        </div>
                    </div>

                    <span class="w-9 h-9 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600
                                 group-hover:bg-white group-hover:shadow-sm transition">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </span>
                </div>

                {{-- bio --}}
                <p class="mt-4 text-[12px] text-slate-600 leading-relaxed clamp-2">
                    {{ $bio }}
                </p>

                {{-- footer --}}
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-500 inline-flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                        Voir le profil
                    </span>

                    <span class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-extrabold">
                        Auteur
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center text-slate-500 py-20 bg-white border border-slate-100 rounded-3xl">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                    <i data-lucide="users" class="w-6 h-6 text-slate-500"></i>
                </div>
                <p class="font-semibold text-slate-700">Aucun auteur trouvé</p>
                <p class="text-sm text-slate-500 mt-1">Essaie un autre nom.</p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION (avec ellipsis) --}}
    @if($authors->hasPages())
        @php
            $current = $authors->currentPage();
            $last = $authors->lastPage();

            // pages à afficher : 1, last, current-1..current+1
            $pages = collect([1, $last, $current-1, $current, $current+1])
                ->filter(fn($p) => $p >= 1 && $p <= $last)
                ->unique()
                ->sort()
                ->values();

            $pageArray = $pages->all();
        @endphp

        <div class="mt-12 flex justify-center">
            <div class="pg flex flex-wrap items-center justify-center gap-2">

                {{-- Prev --}}
                @if ($authors->onFirstPage())
                    <span class="disabled"><span>‹</span></span>
                @else
                    <a href="{{ $authors->previousPageUrl() }}" rel="prev">‹</a>
                @endif

                {{-- Pages + dots --}}
                @for($i=0; $i < count($pageArray); $i++)
                    @php
                        $p = $pageArray[$i];
                        $prev = $i > 0 ? $pageArray[$i-1] : null;
                        $gap = $prev ? ($p - $prev) : 0;
                    @endphp

                    @if($gap > 1)
                        <span class="dots"><span>…</span></span>
                    @endif

                    @if($p == $current)
                        <span class="active"><span>{{ $p }}</span></span>
                    @else
                        <a href="{{ $authors->url($p) }}">{{ $p }}</a>
                    @endif
                @endfor

                {{-- Next --}}
                @if ($authors->hasMorePages())
                    <a href="{{ $authors->nextPageUrl() }}" rel="next">›</a>
                @else
                    <span class="disabled"><span>›</span></span>
                @endif

            </div>
        </div>
    @endif

</div>

@endsection
