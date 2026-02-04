@extends('front.layouts.app')

@section('title', 'Catégories – Fasolivre')

@section('content')

<style>
    :root { --faso-orange:#E0551B; --faso-green:#079C25; }

    .soft-card{
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        box-shadow: 0 10px 25px rgba(2,6,23,.06);
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

    {{-- HEADER --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-b from-white to-[#fff7f2] p-6 sm:p-8 mb-8">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-[var(--faso-orange)]/10 blur-3xl rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-[var(--faso-green)]/10 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-[11px] font-semibold text-slate-700">
                <i data-lucide="sparkles" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                Découvrir
            </div>

            <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900 flex items-center gap-3">
                <i data-lucide="grid-3x3" class="w-9 h-9 text-[var(--faso-orange)]"></i>
                Catégories
            </h1>

            <p class="text-sm text-slate-600 mt-2">
                Explore les univers de lecture • <span class="font-semibold text-slate-900">{{ $categories->total() }}</span> catégories
            </p>
        </div>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
               class="group soft-card rounded-3xl p-5 hover:shadow-xl hover:-translate-y-1 transition relative overflow-hidden">

                <div class="absolute -top-10 -right-10 w-24 h-24 bg-[var(--faso-orange)]/10 blur-2xl rounded-full pointer-events-none"></div>

                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center group-hover:bg-white transition">
                    <i data-lucide="folder" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                </div>

                <h3 class="mt-4 font-extrabold text-slate-900 text-sm truncate group-hover:text-[var(--faso-orange)] transition">
                    {{ $category->name }}
                </h3>

                @if($category->description)
                    <p class="mt-2 text-[12px] text-slate-600 leading-relaxed">
                        {{ \Illuminate\Support\Str::limit($category->description, 70) }}
                    </p>
                @else
                    <p class="mt-2 text-[12px] text-slate-500 leading-relaxed">
                        Explorer les livres de cette catégorie.
                    </p>
                @endif

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-500 inline-flex items-center gap-1.5">
                        <i data-lucide="book-open" class="w-4 h-4 text-[var(--faso-green)]"></i>
                        {{ (int)$category->books_count }} {{ ((int)$category->books_count) > 1 ? 'livres' : 'livre' }}
                    </span>
                    <span class="w-9 h-9 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-white group-hover:shadow-sm transition">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    {{-- PAGINATION (ellipsis) --}}
    @if($categories->hasPages())
        @php
            $current = $categories->currentPage();
            $last = $categories->lastPage();
            $pages = collect([1, $last, $current-1, $current, $current+1])
                ->filter(fn($p) => $p >= 1 && $p <= $last)
                ->unique()->sort()->values()->all();
        @endphp

        <div class="mt-12 flex justify-center">
            <div class="pg flex flex-wrap items-center justify-center gap-2">
                @if ($categories->onFirstPage())
                    <span class="disabled"><span>‹</span></span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" rel="prev">‹</a>
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
                        <a href="{{ $categories->url($p) }}">{{ $p }}</a>
                    @endif
                @endfor

                @if ($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" rel="next">›</a>
                @else
                    <span class="disabled"><span>›</span></span>
                @endif
            </div>
        </div>
    @endif

</div>

@endsection
