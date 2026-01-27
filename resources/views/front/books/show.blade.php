@extends('front.layouts.app')

@section('title', $book->title . ' – Fasolivre')

@section('content')
@php
    $user = auth()->user();

    $category = $book->category;
    $coverUrl = $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');

    $hasPurchase = false;
    $hasActiveSub = false;

    if ($user) {
        $hasPurchase = \App\Models\BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNotNull('purchased_at')
            ->exists();

        $hasActiveSub = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();
    }

    $canRead =
        $book->access_type === 'free'
        || ($book->access_type === 'paid' && $hasPurchase)
        || ($book->access_type === 'subscription' && $hasActiveSub)
        || $hasPurchase;
@endphp

<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('books.index') }}" class="hover:text-[#E0551B] flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Retour
        </a>

        @if($category)
            <span>/</span>
            <a href="{{ route('categories.show', $category->slug) }}" class="hover:text-[#E0551B]">
                {{ $category->name }}
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        <div>
            <img src="{{ $coverUrl }}"
                 alt="{{ $book->title }}"
                 class="rounded-2xl shadow-xl w-full object-cover aspect-[3/4]">

            <div class="mt-6">
                @if($book->access_type == 'free')
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm">
                        <i data-lucide="badge-check" class="w-4 h-4"></i> Gratuit
                    </span>
                @elseif($book->access_type == 'paid')
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i> {{ number_format($book->price,0,',',' ') }} FCFA
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-700 rounded-full text-sm">
                        <i data-lucide="crown" class="w-4 h-4"></i> Abonnement
                    </span>
                @endif
            </div>

            <div class="mt-8 space-y-3">

                @auth

                    @if($canRead)

                        @if($book->pdf_file)
                            <a href="{{ route('read.book', ['slug' => $book->slug]) }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                                      bg-[#079C25] text-white font-medium hover:bg-[#06801f]">
                                <i data-lucide="book-open" class="w-5 h-5"></i> Lire le livre (PDF)
                            </a>
                        @endif

                        @if($book->audio_file)
                            <a href="{{ route('read.audio', $book->slug) }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                                      bg-[#E0551B] text-white font-medium hover:bg-[#c64b19]">
                                <i data-lucide="headphones" class="w-5 h-5"></i> Écouter l'audiobook
                            </a>
                        @endif

                        @if(!$book->pdf_file && !$book->audio_file)
                            <div class="w-full px-5 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-sm">
                                Aucun fichier disponible pour ce livre.
                            </div>
                        @endif

                    @else

                        @if($book->access_type === 'paid')
                            <button type="button" data-book-id="{{ $book->id }}"
                                class="pay-book-btn w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                                       bg-indigo-600 text-white font-medium hover:bg-indigo-700">
                                <i data-lucide="credit-card" class="w-5 h-5"></i> Payer pour lire
                            </button>

                        @elseif($book->access_type === 'subscription')
                            {{-- ✅ FIX: page UI abonnement --}}
                            <a href="{{ route('plans.page') }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                                      bg-orange-600 text-white font-medium hover:bg-orange-700">
                                <i data-lucide="crown" class="w-5 h-5"></i> Voir les abonnements
                            </a>

                            <button type="button" data-book-id="{{ $book->id }}"
                                class="pay-book-btn w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                                       bg-slate-900 text-white font-medium hover:bg-slate-800">
                                <i data-lucide="credit-card" class="w-5 h-5"></i> Acheter ce livre
                            </button>
                        @endif

                        <div class="text-xs text-slate-500 leading-relaxed">
                            Ton accès sera activé automatiquement après confirmation du paiement (via webhook).
                        </div>

                    @endif

                @else
                    <a href="{{ route('login') }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                              bg-indigo-600 text-white hover:bg-indigo-700">
                        <i data-lucide="lock" class="w-5 h-5"></i> Se connecter pour continuer
                    </a>
                @endauth

            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <h1 class="text-3xl font-semibold text-slate-900">{{ $book->title }}</h1>

            <div class="flex items-center gap-4 text-sm text-slate-600">
                <span class="flex items-center gap-1">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                </span>

                <span class="flex items-center gap-1">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    Publié le {{ optional($book->published_at)->format('d M Y') ?? '—' }}
                </span>
            </div>

            <div class="prose max-w-full text-slate-700 text-sm leading-relaxed">
                {!! nl2br(e($book->description)) !!}
            </div>

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

    @if($category)
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

                    <img src="{{ $sim->cover ? asset('storage/'.$sim->cover) : asset('images/placeholder-book.jpg') }}"
                         alt="{{ $sim->title }}"
                         class="w-full h-56 object-cover">

                    <div class="p-3">
                        <h3 class="font-medium text-sm">{{ \Illuminate\Support\Str::limit($sim->title, 40) }}</h3>
                        <p class="text-xs text-gray-500">{{ optional($sim->author)->name ?? '' }}</p>
                    </div>

                </a>

            @endforeach
        </div>
    </div>
    @endif

</div>

@auth
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pay-book-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const bookId = btn.getAttribute('data-book-id');
            btn.disabled = true;

            try {
                const res = await fetch("{{ route('pay.book') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                    },
                    body: JSON.stringify({ book_id: parseInt(bookId, 10) }),
                });

                const data = await res.json();

                if (data.checkout_url) {
                    window.location.href = data.checkout_url;
                    return;
                }

                alert(data.message ?? "Impossible de générer le paiement.");
            } catch (e) {
                alert("Erreur réseau. Réessaie.");
            } finally {
                btn.disabled = false;
            }
        });
    });
});
</script>
@endauth

@endsection
