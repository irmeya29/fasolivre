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
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(226, 232, 240, 0.9);
    }

    .clamp-2{
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Pagination élégante */
    .pg a, .pg span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 13px;
        border: 1px solid rgb(226 232 240);
        background: #fff;
        color: rgb(51 65 85);
        transition: all .2s ease;
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
</style>

@php
    $activeAccess   = request('access', '');
    $activeCategory = request('category', '');
    $activeSort     = request('sort', 'new');
@endphp

<div class="max-w-7xl mx-auto px-4 py-10 lg:py-12">

    {{-- HEADER + RECHERCHE --}}
    <div class="mb-7">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-[11px] font-semibold text-slate-700">
                    <i data-lucide="library" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                    Bibliothèque Fasolivre
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">
                    Tous les livres
                </h1>
                <p class="text-sm text-slate-600">
                    {{ $books->total() }} livres disponibles
                </p>
            </div>

            <form action="{{ route('books.index') }}" method="GET"
                  class="glass w-full lg:w-[520px] rounded-2xl p-2 flex items-center gap-2 shadow-sm">
                <div class="flex items-center gap-2 px-3">
                    <i data-lucide="search" class="w-5 h-5 text-slate-500"></i>
                </div>

                <input type="text" name="q"
                       value="{{ request('q') }}"
                       placeholder="Rechercher un livre, un auteur..."
                       class="bg-transparent flex-1 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none px-1">

                {{-- garder les filtres --}}
                <input type="hidden" name="access" value="{{ request('access') }}">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="hidden" name="sort" value="{{ request('sort','new') }}">

                <button class="px-4 py-2.5 rounded-xl bg-[var(--faso-orange)] text-white text-xs font-extrabold
                               shadow hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                    Rechercher
                </button>
            </form>
        </div>
    </div>

    {{-- BARRE FILTRES (clean, légère) --}}
    <div class="glass rounded-2xl p-3 sm:p-4 mb-8 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">

            {{-- Chips accès (fines) --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $chipBase = "px-3.5 py-2 rounded-xl text-sm font-bold border transition inline-flex items-center gap-2";
                    $chipOn = "bg-slate-900 text-white border-slate-900";
                    $chipOff = "bg-white text-slate-700 border-slate-200 hover:border-slate-300 hover:-translate-y-[1px] hover:shadow-sm";
                @endphp

                <a href="{{ route('books.index', array_merge(request()->except('page','access'), ['access' => null])) }}"
                   class="{{ $chipBase }} {{ $activeAccess==='' ? $chipOn : $chipOff }}">
                    Tous
                </a>

                <a href="{{ route('books.index', array_merge(request()->except('page'), ['access' => 'free'])) }}"
                   class="{{ $chipBase }} {{ $activeAccess==='free' ? $chipOn : $chipOff }}">
                    <i data-lucide="gift" class="w-4 h-4"></i> Gratuits
                </a>

                <a href="{{ route('books.index', array_merge(request()->except('page'), ['access' => 'paid'])) }}"
                   class="{{ $chipBase }} {{ $activeAccess==='paid' ? $chipOn : $chipOff }}">
                    <i data-lucide="wallet" class="w-4 h-4"></i> Payants
                </a>

                <a href="{{ route('books.index', array_merge(request()->except('page'), ['access' => 'subscription'])) }}"
                   class="{{ $chipBase }} {{ $activeAccess==='subscription' ? $chipOn : $chipOff }}">
                    <i data-lucide="crown" class="w-4 h-4"></i> Abonnement
                </a>
            </div>

            {{-- Selects + actions --}}
            <form method="GET" action="{{ route('books.index') }}" class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto lg:ml-auto">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="access" value="{{ request('access') }}">

                <select name="category"
                        class="w-full sm:w-[240px] px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-700
                               focus:outline-none focus:ring-2 focus:ring-[var(--faso-orange)]/20">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ $activeCategory===$cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="sort"
                        class="w-full sm:w-[200px] px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-700
                               focus:outline-none focus:ring-2 focus:ring-[var(--faso-orange)]/20">
                    <option value="new" {{ $activeSort==='new' ? 'selected' : '' }}>Nouveautés</option>
                    <option value="old" {{ $activeSort==='old' ? 'selected' : '' }}>Plus anciens</option>
                    <option value="price_asc" {{ $activeSort==='price_asc' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="price_desc" {{ $activeSort==='price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                </select>

                <button class="px-5 py-2.5 rounded-2xl bg-[var(--faso-orange)] text-white text-sm font-extrabold
                               shadow hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                    Appliquer
                </button>

                <a href="{{ route('books.index') }}"
                   class="px-5 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-extrabold hover:bg-slate-200 text-center">
                    Réinitialiser
                </a>
            </form>
        </div>
    </div>

    {{-- GRID LIVRES (clean : 1 seul badge = prix) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
        @forelse($books as $book)
            @php
                $coverUrl = $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');

                $isFree = $book->access_type === 'free';
                $isPaid = $book->access_type === 'paid';
                $isSub  = $book->access_type === 'subscription';

                $priceLabel = $isFree ? 'Gratuit' : ($isPaid ? number_format($book->price, 0, ',', ' ').' FCFA' : 'Abonnement');
                $priceIcon  = $isFree ? 'gift' : ($isPaid ? 'wallet' : 'crown');

                $isNew = $book->published_at && $book->published_at->gte(now()->subDays(7));
            @endphp

            <a href="{{ route('books.show', $book->slug) }}"
               class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm
                      hover:shadow-xl hover:-translate-y-1 transition">

                <div class="relative">
                    <img src="{{ $coverUrl }}"
                         alt="{{ $book->title }}"
                         loading="lazy"
                         class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">

                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                    @if($isNew)
                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-white/90 text-[var(--faso-orange)]
                                     text-[10px] font-extrabold backdrop-blur border border-white/70">
                            Nouveau
                        </span>
                    @endif

                    {{-- badge unique : prix --}}
                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold
                                     {{ $isFree ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-900' }}
                                     backdrop-blur border border-white/60 shadow-lg shadow-black/10">
                            <i data-lucide="{{ $priceIcon }}"
                               class="w-4 h-4 {{ $isFree ? '' : ($isPaid ? 'text-[var(--faso-orange)]' : 'text-indigo-600') }}"></i>
                            {{ $priceLabel }}
                        </span>

                        <span class="w-10 h-10 rounded-2xl bg-white/15 border border-white/25 backdrop-blur
                                     flex items-center justify-center text-white
                                     group-hover:bg-white/25 transition">
                            <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                        </span>
                    </div>
                </div>

                <div class="p-4 space-y-2">
                    <h3 class="font-extrabold text-[13px] sm:text-sm text-slate-900 leading-snug clamp-2">
                        {{ $book->title }}
                    </h3>

                    <p class="text-[11px] text-slate-500 flex items-center gap-1.5 truncate">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                    </p>
                </div>

                <div class="absolute inset-0 rounded-3xl ring-0 ring-[var(--faso-orange)]/0 group-hover:ring-2 group-hover:ring-[var(--faso-orange)]/18 transition pointer-events-none"></div>
            </a>
        @empty
            <div class="col-span-full text-center text-slate-500 py-20 bg-white border border-slate-100 rounded-3xl">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                    <i data-lucide="search-x" class="w-6 h-6 text-slate-500"></i>
                </div>
                <p class="font-semibold text-slate-700">Aucun livre trouvé</p>
                <p class="text-sm text-slate-500 mt-1">Essaie un autre mot-clé.</p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION (jolie) --}}
    @if($books->hasPages())
        <div class="mt-12 flex justify-center">
            <div class="pg flex flex-wrap items-center justify-center gap-2">
                {{-- Prev --}}
                @if ($books->onFirstPage())
                    <span class="disabled"><span>‹</span></span>
                @else
                    <a href="{{ $books->previousPageUrl() }}" rel="prev">‹</a>
                @endif

                {{-- Pages --}}
                @foreach ($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                    @if ($page == $books->currentPage())
                        <span class="active"><span>{{ $page }}</span></span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($books->hasMorePages())
                    <a href="{{ $books->nextPageUrl() }}" rel="next">›</a>
                @else
                    <span class="disabled"><span>›</span></span>
                @endif
            </div>
        </div>
    @endif

</div>

@endsection
