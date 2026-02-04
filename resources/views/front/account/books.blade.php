@extends('front.layouts.app')

@section('title', 'Mes livres – Fasolivre')

@section('content')

<style>
    :root { --faso-orange:#E0551B; --faso-green:#079C25; }

    .soft-card{
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        box-shadow: 0 10px 25px rgba(2,6,23,.06);
    }
    .clamp-2{
        display:-webkit-box;
        -webkit-line-clamp:2;
        -webkit-box-orient:vertical;
        overflow:hidden;
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

@php
    $user = auth()->user();
    $tab = request('tab', 'purchases'); // purchases | subscription | progress | favorites

    // Abonnement actif
    $activeSub = \App\Models\Subscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->whereNotNull('ends_at')
        ->where('ends_at', '>', now())
        ->exists();

    // ====== ONGLET: Achats confirmés ======
    $purchasesQuery = \App\Models\BookPurchase::with(['book.author'])
        ->where('user_id', $user->id)
        ->whereNotNull('purchased_at')
        ->latest('purchased_at');

    $purchases = (clone $purchasesQuery)->paginate(12, ['*'], 'p_page')->withQueryString();
    $purchasedCount = (clone $purchasesQuery)->count();

    // ====== ONGLET: Abonnement (livres access_type=subscription) ======
    $subBooks = null;
    $subCount = 0;
    if ($activeSub) {
        $subBooks = \App\Models\Book::with(['author'])
            ->where('status','published')
            ->where('access_type','subscription')
            ->orderByRaw("COALESCE(published_at, created_at) DESC")
            ->paginate(12, ['*'], 's_page')
            ->withQueryString();

        $subCount = \App\Models\Book::where('status','published')->where('access_type','subscription')->count();
    }

    // ====== ONGLET: En cours (pivot progress > 0) ======
    $progressBooks = $user->books()
        ->with('author')
        ->wherePivot('progress', '>', 0)
        ->orderByDesc('book_user.updated_at')
        ->paginate(12, ['books.*'], 'pr_page')
        ->withQueryString();

    $progressCount = $user->books()->wherePivot('progress', '>', 0)->count();

    // ====== ONGLET: Favoris (pivot is_favorite=1) ======
    $favoriteBooks = $user->books()
        ->with('author')
        ->wherePivot('is_favorite', true)
        ->orderByDesc('book_user.updated_at')
        ->paginate(12, ['books.*'], 'f_page')
        ->withQueryString();

    $favoriteCount = $user->books()->wherePivot('is_favorite', true)->count();

    // petits helpers
    $tabBase = "px-4 py-2.5 rounded-2xl text-sm font-extrabold border transition inline-flex items-center gap-2";
    $tabOn   = "bg-slate-900 text-white border-slate-900";
    $tabOff  = "bg-white text-slate-700 border-slate-200 hover:bg-slate-50";
@endphp

<div class="max-w-7xl mx-auto px-4 py-10 lg:py-12">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-b from-white to-[#fff7f2] p-6 sm:p-8 mb-8">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-[var(--faso-orange)]/10 blur-3xl rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-[var(--faso-green)]/10 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-[11px] font-semibold text-slate-700">
                    <i data-lucide="book-open" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                    Bibliothèque personnelle
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Mes livres</h1>

                <p class="text-sm text-slate-600 flex flex-wrap gap-x-2 gap-y-1">
                    <span><span class="font-semibold text-slate-900">{{ $purchasedCount }}</span> achetés</span>
                    <span class="text-slate-400">•</span>
                    <span><span class="font-semibold text-slate-900">{{ $progressCount }}</span> en cours</span>
                    <span class="text-slate-400">•</span>
                    <span><span class="font-semibold text-slate-900">{{ $favoriteCount }}</span> favoris</span>
                    <span class="text-slate-400">•</span>
                    <span>Abonnement : <span class="font-semibold {{ $activeSub ? 'text-[var(--faso-green)]' : 'text-slate-700' }}">{{ $activeSub ? 'Actif' : 'Inactif' }}</span></span>
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('books.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white font-extrabold text-sm hover:bg-slate-800 transition">
                    <i data-lucide="search" class="w-5 h-5"></i>
                    Explorer
                </a>

                <a href="{{ route('account.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-700 font-extrabold text-sm
                          hover:border-[var(--faso-orange)] hover:text-[var(--faso-orange)] hover:shadow-sm transition">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    Compte
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="soft-card rounded-3xl p-3 sm:p-4 mb-8">
        <div class="flex flex-wrap gap-2">

            <a href="{{ route('account.books', array_merge(request()->except('tab','p_page','s_page','pr_page','f_page'), ['tab' => 'purchases'])) }}"
               class="{{ $tabBase }} {{ $tab==='purchases' ? $tabOn : $tabOff }}">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                Achetés
                @if($purchasedCount > 0)
                    <span class="ml-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold border border-slate-200">
                        {{ $purchasedCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('account.books', array_merge(request()->except('tab','p_page','s_page','pr_page','f_page'), ['tab' => 'progress'])) }}"
               class="{{ $tabBase }} {{ $tab==='progress' ? $tabOn : $tabOff }}">
                <i data-lucide="play" class="w-4 h-4"></i>
                En cours
                @if($progressCount > 0)
                    <span class="ml-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-extrabold border border-emerald-100">
                        {{ $progressCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('account.books', array_merge(request()->except('tab','p_page','s_page','pr_page','f_page'), ['tab' => 'favorites'])) }}"
               class="{{ $tabBase }} {{ $tab==='favorites' ? $tabOn : $tabOff }}">
                <i data-lucide="heart" class="w-4 h-4"></i>
                Favoris
                @if($favoriteCount > 0)
                    <span class="ml-1 px-2 py-0.5 rounded-full bg-orange-50 text-[var(--faso-orange)] text-[10px] font-extrabold border border-orange-100">
                        {{ $favoriteCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('account.books', array_merge(request()->except('tab','p_page','s_page','pr_page','f_page'), ['tab' => 'subscription'])) }}"
               class="{{ $tabBase }} {{ $tab==='subscription' ? $tabOn : $tabOff }}">
                <i data-lucide="crown" class="w-4 h-4"></i>
                Abonnement
                @if(!$activeSub)
                    <span class="ml-1 px-2 py-0.5 rounded-full bg-orange-50 text-[var(--faso-orange)] text-[10px] font-extrabold border border-orange-100">
                        Inactif
                    </span>
                @endif
            </a>

        </div>
    </div>

    {{-- ===== CONTENT AREA ===== --}}
    @php
        // helper: pagination renderer
        $renderPagination = function($paginator){
            if(!$paginator || !$paginator->hasPages()) return '';
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $pages = collect([1, $last, $current-1, $current, $current+1])
                ->filter(fn($p) => $p >= 1 && $p <= $last)
                ->unique()->sort()->values()->all();

            ob_start(); ?>
            <div class="mt-12 flex justify-center">
                <div class="pg flex flex-wrap items-center justify-center gap-2">
                    <?php if ($paginator->onFirstPage()): ?>
                        <span class="disabled"><span>‹</span></span>
                    <?php else: ?>
                        <a href="<?= $paginator->previousPageUrl() ?>" rel="prev">‹</a>
                    <?php endif; ?>

                    <?php for($i=0; $i<count($pages); $i++):
                        $p = $pages[$i];
                        $prev = $i>0 ? $pages[$i-1] : null;
                        if($prev && $p-$prev>1): ?>
                            <span class="dots"><span>…</span></span>
                        <?php endif; ?>

                        <?php if($p == $current): ?>
                            <span class="active"><span><?= $p ?></span></span>
                        <?php else: ?>
                            <a href="<?= $paginator->url($p) ?>"><?= $p ?></a>
                        <?php endif; ?>

                    <?php endfor; ?>

                    <?php if ($paginator->hasMorePages()): ?>
                        <a href="<?= $paginator->nextPageUrl() ?>" rel="next">›</a>
                    <?php else: ?>
                        <span class="disabled"><span>›</span></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php return ob_get_clean();
        };
    @endphp

    {{-- Achats --}}
    @if($tab === 'purchases')
        @if($purchases->count() === 0)
            <div class="bg-white border border-slate-100 rounded-3xl p-10 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-slate-500"></i>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900">Aucun livre acheté</h2>
                <p class="text-sm text-slate-600 mt-2 max-w-xl mx-auto">Explore la bibliothèque et commence ta prochaine lecture.</p>
                <a href="{{ route('books.index') }}"
                   class="mt-5 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold text-sm hover:bg-slate-800 transition">
                    <i data-lucide="library" class="w-5 h-5"></i> Explorer les livres
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
                @foreach($purchases as $purchase)
                    @php
                        $book = $purchase->book;
                        $cover = $book && $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');
                        $isFree = $book && $book->access_type === 'free';
                        $isPaid = $book && $book->access_type === 'paid';
                        $priceLabel = $book ? ($isFree ? 'Gratuit' : ($isPaid ? number_format($book->price,0,',',' ').' FCFA' : 'Abonnement')) : '—';
                        $priceIcon  = $book ? ($isFree ? 'gift' : ($isPaid ? 'wallet' : 'crown')) : 'tag';
                    @endphp
                    @if($book)
                        <a href="{{ route('books.show', $book->slug) }}"
                           class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition">

                            <div class="relative">
                                <img src="{{ $cover }}" alt="{{ $book->title }}" loading="lazy"
                                     class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">
                                <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

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
                                    {{ optional($book->author)->name ?? 'Auteur' }}
                                </p>

                                <div class="pt-2 flex gap-2">
                                    @if($book->pdf_file)
                                        <a href="{{ route('read.book', $book->slug) }}"
                                           class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-2xl
                                                  bg-[var(--faso-green)] text-white text-xs font-extrabold hover:opacity-95 transition">
                                            <i data-lucide="book-open" class="w-4 h-4"></i> Lire
                                        </a>
                                    @endif
                                    @if($book->audio_file)
                                        <a href="{{ route('read.audio', $book->slug) }}"
                                           class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-2xl
                                                  bg-[var(--faso-orange)] text-white text-xs font-extrabold hover:opacity-95 transition">
                                            <i data-lucide="headphones" class="w-4 h-4"></i> Écouter
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="absolute inset-0 rounded-3xl ring-0 ring-[var(--faso-orange)]/0 group-hover:ring-2 group-hover:ring-[var(--faso-orange)]/18 transition pointer-events-none"></div>
                        </a>
                    @endif
                @endforeach
            </div>

            {!! $renderPagination($purchases) !!}
        @endif
    @endif

    {{-- En cours (pivot progress) --}}
    @if($tab === 'progress')
        @if($progressBooks->count() === 0)
            <div class="bg-white border border-slate-100 rounded-3xl p-10 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                    <i data-lucide="play" class="w-6 h-6 text-slate-500"></i>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900">Aucune lecture en cours</h2>
                <p class="text-sm text-slate-600 mt-2 max-w-xl mx-auto">Commence un livre, et retrouve-le ici pour reprendre.</p>
                <a href="{{ route('books.index') }}"
                   class="mt-5 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold text-sm hover:bg-slate-800 transition">
                    <i data-lucide="library" class="w-5 h-5"></i> Explorer
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
                @foreach($progressBooks as $book)
                    @php
                        $cover = $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');
                        $progress = (int) ($book->pivot?->progress ?? 0);
                        $isFree = $book->access_type === 'free';
                        $isPaid = $book->access_type === 'paid';
                        $priceLabel = $isFree ? 'Gratuit' : ($isPaid ? number_format($book->price,0,',',' ').' FCFA' : 'Abonnement');
                        $priceIcon  = $isFree ? 'gift' : ($isPaid ? 'wallet' : 'crown');
                    @endphp

                    <a href="{{ route('books.show', $book->slug) }}"
                       class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition">

                        <div class="relative">
                            <img src="{{ $cover }}" alt="{{ $book->title }}" loading="lazy"
                                 class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">
                            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold
                                             {{ $isFree ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-900' }}
                                             backdrop-blur border border-white/60 shadow-lg shadow-black/10">
                                    <i data-lucide="{{ $priceIcon }}"
                                       class="w-4 h-4 {{ $isFree ? '' : ($isPaid ? 'text-[var(--faso-orange)]' : 'text-indigo-600') }}"></i>
                                    {{ $priceLabel }}
                                </span>

                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold
                                             bg-white/90 text-slate-900 backdrop-blur border border-white/60 shadow-lg shadow-black/10">
                                    {{ $progress }}%
                                </span>
                            </div>
                        </div>

                        <div class="p-4 space-y-2">
                            <h3 class="font-extrabold text-[13px] sm:text-sm text-slate-900 leading-snug clamp-2">
                                {{ $book->title }}
                            </h3>

                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5 truncate">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                {{ optional($book->author)->name ?? 'Auteur' }}
                            </p>

                            {{-- ✅ EXACT ton bloc pivot progress --}}
                            @if($book->pivot?->progress)
                                <div class="mt-2">
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1 overflow-hidden">
                                        <div class="h-1.5 bg-[#079C25]" style="width: {{ $book->pivot->progress }}%"></div>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $book->pivot->progress }}% lu
                                    </p>
                                </div>
                            @endif

                            <div class="pt-2">
                                <a href="{{ route('read.book', $book->slug) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-2xl
                                          bg-[var(--faso-green)] text-white text-xs font-extrabold hover:opacity-95 transition">
                                    <i data-lucide="book-open" class="w-4 h-4"></i> Reprendre
                                </a>
                            </div>
                        </div>

                        <div class="absolute inset-0 rounded-3xl ring-0 ring-[var(--faso-orange)]/0 group-hover:ring-2 group-hover:ring-[var(--faso-orange)]/18 transition pointer-events-none"></div>
                    </a>
                @endforeach
            </div>

            {!! $renderPagination($progressBooks) !!}
        @endif
    @endif

    {{-- Favoris --}}
    @if($tab === 'favorites')
        @if($favoriteBooks->count() === 0)
            <div class="bg-white border border-slate-100 rounded-3xl p-10 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                    <i data-lucide="heart" class="w-6 h-6 text-slate-500"></i>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900">Aucun favori</h2>
                <p class="text-sm text-slate-600 mt-2 max-w-xl mx-auto">Ajoute des livres en favoris pour les retrouver rapidement.</p>
                <a href="{{ route('books.index') }}"
                   class="mt-5 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-900 text-white font-extrabold text-sm hover:bg-slate-800 transition">
                    <i data-lucide="library" class="w-5 h-5"></i> Explorer
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
                @foreach($favoriteBooks as $book)
                    @php
                        $cover = $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');
                        $isFree = $book->access_type === 'free';
                        $isPaid = $book->access_type === 'paid';
                        $priceLabel = $isFree ? 'Gratuit' : ($isPaid ? number_format($book->price,0,',',' ').' FCFA' : 'Abonnement');
                        $priceIcon  = $isFree ? 'gift' : ($isPaid ? 'wallet' : 'crown');
                    @endphp

                    <a href="{{ route('books.show', $book->slug) }}"
                       class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition">
                        <div class="relative">
                            <img src="{{ $cover }}" alt="{{ $book->title }}" loading="lazy"
                                 class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">
                            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                            <div class="absolute top-3 right-3 inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/90 text-[var(--faso-orange)]
                                        text-[11px] font-extrabold backdrop-blur border border-white/60 shadow-lg shadow-black/10">
                                <i data-lucide="heart" class="w-4 h-4"></i>
                                Favori
                            </div>

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
                                {{ optional($book->author)->name ?? 'Auteur' }}
                            </p>

                            @if($book->pivot?->progress)
                                <div class="mt-2">
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1 overflow-hidden">
                                        <div class="h-1.5 bg-[#079C25]" style="width: {{ $book->pivot->progress }}%"></div>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $book->pivot->progress }}% lu
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="absolute inset-0 rounded-3xl ring-0 ring-[var(--faso-orange)]/0 group-hover:ring-2 group-hover:ring-[var(--faso-orange)]/18 transition pointer-events-none"></div>
                    </a>
                @endforeach
            </div>

            {!! $renderPagination($favoriteBooks) !!}
        @endif
    @endif

    {{-- Abonnement --}}
    @if($tab === 'subscription')
        @if(!$activeSub)
            <div class="bg-white border border-slate-100 rounded-3xl p-10 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                    <i data-lucide="crown" class="w-6 h-6 text-slate-500"></i>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900">Abonnement inactif</h2>
                <p class="text-sm text-slate-600 mt-2 max-w-xl mx-auto">
                    Active un abonnement pour accéder à une sélection de livres inclus.
                </p>
                <a href="{{ route('plans.page') }}"
                   class="mt-5 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[var(--faso-orange)] text-white font-extrabold text-sm hover:shadow-lg hover:-translate-y-0.5 transition">
                    <i data-lucide="sparkles" class="w-5 h-5"></i> Voir les abonnements
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
                @forelse($subBooks as $book)
                    @php
                        $cover = $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');
                    @endphp

                    <a href="{{ route('books.show', $book->slug) }}"
                       class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition">
                        <div class="relative">
                            <img src="{{ $cover }}" alt="{{ $book->title }}" loading="lazy"
                                 class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">
                            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold
                                             bg-white/90 text-slate-900 backdrop-blur border border-white/60 shadow-lg shadow-black/10">
                                    <i data-lucide="crown" class="w-4 h-4 text-indigo-600"></i>
                                    Abonnement
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
                                {{ optional($book->author)->name ?? 'Auteur' }}
                            </p>

                            <div class="pt-1">
                                @if($book->pdf_file)
                                    <span class="text-[11px] font-extrabold text-[var(--faso-green)] inline-flex items-center gap-1">
                                        <i data-lucide="book-open" class="w-4 h-4"></i> PDF
                                    </span>
                                @elseif($book->audio_file)
                                    <span class="text-[11px] font-extrabold text-[var(--faso-orange)] inline-flex items-center gap-1">
                                        <i data-lucide="headphones" class="w-4 h-4"></i> Audio
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="absolute inset-0 rounded-3xl ring-0 ring-[var(--faso-orange)]/0 group-hover:ring-2 group-hover:ring-[var(--faso-orange)]/18 transition pointer-events-none"></div>
                    </a>
                @empty
                    <div class="col-span-full text-center text-slate-500 py-20 bg-white border border-slate-100 rounded-3xl">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                            <i data-lucide="library" class="w-6 h-6 text-slate-500"></i>
                        </div>
                        <p class="font-extrabold text-slate-900">Aucun livre abonnement pour le moment</p>
                        <p class="text-sm text-slate-600 mt-1">La sélection va s’agrandir.</p>
                    </div>
                @endforelse
            </div>

            {!! $renderPagination($subBooks) !!}
        @endif
    @endif

</div>

@endsection
