@extends('front.layouts.app')

@section('title', $book->title . ' – Fasolivre')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- ===========================
         BREADCRUMB
    ============================ --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('books.index') }}" class="hover:text-[#E0551B] flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Retour
        </a>
        <span>/</span>
        <a href="{{ route('categories.show', $book->category->slug) }}" class="hover:text-[#E0551B]">
            {{ $book->category->name }}
        </a>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        {{-- ===========================
             COVER + CTA
        ============================ --}}
        <div>
            {{-- Cover --}}
            <img src="{{ asset('storage/'.$book->cover) }}"
                 class="rounded-2xl shadow-xl w-full object-cover aspect-[3/4]">

            {{-- Access badge --}}
            <div class="mt-6">
                @if($book->access_type == 'free')
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm">
                        <i data-lucide="badge-check" class="w-4 h-4"></i> Gratuit
                    </span>
                @elseif($book->access_type == 'paid')
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i> {{ number_format($book->price,0) }} FCFA
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-700 rounded-full text-sm">
                        <i data-lucide="crown" class="w-4 h-4"></i> Abonnement
                    </span>
                @endif
            </div>


            {{-- CTA --}}
            <div class="mt-8 space-y-3">

                @auth
                    {{-- PDF Reader --}}
                    @if($book->pdf_file)
                        <a href="{{ route('read.book', ['slug' => $book->slug]) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                                  bg-[#079C25] text-white font-medium hover:bg-[#06801f]">
                            <i data-lucide="book-open" class="w-5 h-5"></i> Lire le livre (PDF)
                        </a>
                    @endif

                    {{-- Audio Reader --}}
                    @if($book->audio_file)
                        <a href="{{ route('read.audio', $book->slug) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                                  bg-[#E0551B] text-white font-medium hover:bg-[#c64b19]">
                            <i data-lucide="headphones" class="w-5 h-5"></i> Écouter l'audiobook
                        </a>
                    @endif

                @else
                    <a href="{{ route('login') }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                              bg-indigo-600 text-white hover:bg-indigo-700">
                        <i data-lucide="lock" class="w-5 h-5"></i> Se connecter pour lire
                    </a>
                @endauth

            </div>

        </div>



        {{-- ===========================
             BOOK DETAILS
        ============================ --}}
        <div class="lg:col-span-2 space-y-6">

            <h1 class="text-3xl font-semibold text-slate-900">{{ $book->title }}</h1>

            <div class="flex items-center gap-4 text-sm text-slate-600">
                <span class="flex items-center gap-1">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    {{ $book->author->name ?? 'Auteur inconnu' }}
                </span>

                <span class="flex items-center gap-1">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    Publié le {{ optional($book->published_at)->format('d M Y') ?? '—' }}
                </span>
            </div>

            {{-- Description --}}
            <div class="prose max-w-full text-slate-700 text-sm leading-relaxed">
                {!! nl2br(e($book->description)) !!}
            </div>

            {{-- Format --}}
            <div class="pt-4">
                <h3 class="text-sm font-semibold text-slate-900 mb-2">Format disponible</h3>

                <div class="flex gap-2 text-sm">
                    @if($book->pdf_file)
                        <span class="px-3 py-1 rounded-lg bg-slate-100">PDF</span>
                    @endif

                    @if($book->audio_file)
                        <span class="px-3 py-1 rounded-lg bg-slate-100">Audio</span>
                    @endif
                </div>
            </div>

        </div>

    </div>



    {{-- ===========================
         LIVRES SIMILAIRES
    ============================ --}}
    <div class="mt-16">
        <h2 class="text-xl font-semibold text-slate-900 mb-6 flex items-center gap-2">
            <i data-lucide="sparkles" class="w-5 h-5 text-[#E0551B]"></i>
            Livres similaires
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">

            @foreach(
                \App\Models\Book::where('category_id', $book->category_id)
                ->where('id','!=',$book->id)
                ->published()
                ->take(10)->get()
            as $sim)

                <a href="{{ route('books.show', $sim->slug) }}"
                   class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                    <img src="{{ asset('storage/'.$sim->cover) }}"
                         class="w-full h-56 object-cover">

                    <div class="p-3">
                        <h3 class="font-medium text-sm">{{ Str::limit($sim->title, 40) }}</h3>
                        <p class="text-xs text-gray-500">{{ $sim->author->name ?? '' }}</p>
                    </div>

                </a>

            @endforeach

        </div>
    </div>

</div>

@endsection
