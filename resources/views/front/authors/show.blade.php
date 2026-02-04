@extends('front.layouts.app')

@section('title', $author->name . ' – Auteur africain')

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
</style>

@php
    $avatar = $author->photo
        ? asset('storage/'.$author->photo)
        : 'https://ui-avatars.com/api/?name='.urlencode($author->name).'&size=256&background=E0551B&color=fff';

    $booksCount = isset($author->books_count) ? (int)$author->books_count : (isset($author->books) ? $author->books->count() : 0);

    // socials dispo
    $hasSocial = $author->website || $author->facebook || $author->instagram;
@endphp

<div class="max-w-7xl mx-auto px-4 py-10 lg:py-12">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('authors.index.front') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[var(--faso-orange)]">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour aux auteurs
        </a>
    </div>

    {{-- HERO AUTHOR --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-b from-white to-[#fff7f2] p-6 sm:p-8">
        <div class="absolute -top-12 -left-12 w-80 h-80 bg-[var(--faso-orange)]/10 blur-3xl rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-12 w-80 h-80 bg-[var(--faso-green)]/10 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
            {{-- Photo --}}
            <div class="relative">
                <div class="absolute -inset-3 bg-[var(--faso-orange)]/10 blur-2xl rounded-3xl"></div>
                <img src="{{ $avatar }}"
                     alt="{{ $author->name }}"
                     class="relative w-28 h-28 sm:w-32 sm:h-32 lg:w-36 lg:h-36 rounded-3xl object-cover border border-white shadow-sm">
            </div>

            {{-- Infos --}}
            <div class="flex-1 min-w-0 space-y-4">
                <div class="space-y-1">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                        {{ $author->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 border border-slate-100 text-slate-700 font-semibold">
                            <i data-lucide="book-open" class="w-4 h-4 text-[var(--faso-green)]"></i>
                            {{ $booksCount }} {{ $booksCount > 1 ? 'livres' : 'livre' }}
                        </span>

                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 border border-slate-100 text-slate-700 font-semibold">
                            <i data-lucide="sparkles" class="w-4 h-4 text-[var(--faso-orange)]"></i>
                            Auteur Fasolivre
                        </span>
                    </div>
                </div>

                {{-- Bio --}}
                <div class="soft-card rounded-3xl p-5 sm:p-6">
                    <p class="text-sm text-slate-700 leading-relaxed">
                        {!! nl2br(e($author->bio ?? "Biographie indisponible pour le moment.")) !!}
                    </p>
                </div>

                {{-- Social links --}}
                @if($hasSocial)
                    <div class="flex flex-wrap items-center gap-2">
                        @if($author->website)
                            <a href="{{ $author->website }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-700
                                      hover:border-[var(--faso-orange)] hover:text-[var(--faso-orange)] hover:shadow-sm transition">
                                <i data-lucide="globe" class="w-4 h-4"></i>
                                Site web
                            </a>
                        @endif

                        @if($author->facebook)
                            <a href="{{ $author->facebook }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-700
                                      hover:border-[var(--faso-orange)] hover:text-[var(--faso-orange)] hover:shadow-sm transition">
                                <i data-lucide="facebook" class="w-4 h-4"></i>
                                Facebook
                            </a>
                        @endif

                        @if($author->instagram)
                            <a href="{{ $author->instagram }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-700
                                      hover:border-[var(--faso-orange)] hover:text-[var(--faso-orange)] hover:shadow-sm transition">
                                <i data-lucide="instagram" class="w-4 h-4"></i>
                                Instagram
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- BOOKS --}}
    <section class="mt-12">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="library" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                    Livres de {{ $author->name }}
                </h2>
                <p class="text-sm text-slate-600 mt-1">
                    Découvre ses publications disponibles sur Fasolivre.
                </p>
            </div>

            <span class="text-sm font-semibold text-slate-600">
                {{ $booksCount }} {{ $booksCount > 1 ? 'livres' : 'livre' }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
            @forelse($author->books as $book)
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
                            {{ $author->name }}
                        </p>
                    </div>

                    <div class="absolute inset-0 rounded-3xl ring-0 ring-[var(--faso-orange)]/0 group-hover:ring-2 group-hover:ring-[var(--faso-orange)]/18 transition pointer-events-none"></div>
                </a>
            @empty
                <div class="col-span-full text-center text-slate-500 py-20 bg-white border border-slate-100 rounded-3xl">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 mb-3">
                        <i data-lucide="book-open" class="w-6 h-6 text-slate-500"></i>
                    </div>
                    <p class="font-semibold text-slate-700">Aucun livre publié pour le moment</p>
                    <p class="text-sm text-slate-500 mt-1">Reviens plus tard pour découvrir ses nouveautés.</p>
                </div>
            @endforelse
        </div>
    </section>

</div>

@endsection
