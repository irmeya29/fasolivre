@extends('front.layouts.app')

@section('title', $category->name . ' – Catégorie')

@section('content')

<style>
    :root { --faso-orange:#E0551B; --faso-green:#079C25; }

    .clamp-2{
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Pagination */
    .pg a, .pg span {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:40px; height:40px; padding:0 12px;
        border-radius:14px; font-weight:800; font-size:13px;
        border:1px solid rgb(226 232 240); background:#fff; color:rgb(51 65 85);
        transition:all .2s ease; user-select:none;
    }
    .pg a:hover { transform:translateY(-1px); box-shadow:0 10px 25px rgba(2,6,23,.08); border-color:rgba(224,85,27,.25); color:var(--faso-orange); }
    .pg .active span { background:var(--faso-orange); color:#fff; border-color:rgba(224,85,27,.35); box-shadow:0 10px 25px rgba(224,85,27,.22); }
    .pg .disabled span { opacity:.45; cursor:not-allowed; }
    .pg .dots span { border-style:dashed; opacity:.7; }
</style>

<div class="max-w-7xl mx-auto px-4 py-10 lg:py-12">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('categories.index.front') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[var(--faso-orange)]">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour aux catégories
        </a>
    </div>

    <div class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-b from-white to-[#fff7f2] p-6 sm:p-8 mb-8">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-[var(--faso-orange)]/10 blur-3xl rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-[var(--faso-green)]/10 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-[11px] font-semibold text-slate-700">
                <i data-lucide="folder" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                Catégorie
            </div>

            <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">
                {{ $category->name }}
            </h1>

            @if($category->description)
                <p class="text-sm text-slate-600 mt-2 max-w-3xl">
                    {{ $category->description }}
                </p>
            @endif
        </div>
    </div>

    {{-- Livres --}}
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

                    {{-- badge unique prix --}}
                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold
                                     {{ $isFree ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-900' }}
                                     backdrop-blur border border-white/60 shadow-lg shadow-black/10">
                            <i data-lucide="{{ $priceIcon }}"
                               class="w-4 h-4 {{ $isFree ? '' : ($isPaid ? 'text-[var(--faso-orange)]' : 'text-indigo-600') }}"></i>
                            {{ $priceLabel }}
                        </span>

                        <span class="w-10 h-10 rounded-2xl bg-white/15 border border-white/25 backdrop-blur
                                     flex items-center justify-center text-white group-hover:bg-white/25 transition">
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
                    <i data-lucide="book-open" class="w-6 h-6 text-slate-500"></i>
                </div>
                <p class="font-semibold text-slate-700">Aucun livre disponible</p>
                <p class="text-sm text-slate-500 mt-1">Reviens plus tard, de nouveaux titres arrivent.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($books->hasPages())
        @php
            $current = $books->currentPage();
            $last = $books->lastPage();
            $pages = collect([1, $last, $current-1, $current, $current+1])
                ->filter(fn($p) => $p >= 1 && $p <= $last)
                ->unique()->sort()->values()->all();
        @endphp

        <div class="mt-12 flex justify-center">
            <div class="pg flex flex-wrap items-center justify-center gap-2">
                @if ($books->onFirstPage())
                    <span class="disabled"><span>‹</span></span>
                @else
                    <a href="{{ $books->previousPageUrl() }}" rel="prev">‹</a>
                @endif

                @for($i=0; $i < count($pages); $i++)
                    @php
                        $p = $pages[$i];
                        $prev = $i > 0 ? $pages[$i-1] : null;
                    @endphp

                    @if($prev && $p - $prev > 1)
                        <span class="dots"><span>…</span></span>
                    @endif

                    @if($p == $current)
                        <span class="active"><span>{{ $p }}</span></span>
                    @else
                        <a href="{{ $books->url($p) }}">{{ $p }}</a>
                    @endif
                @endfor

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
