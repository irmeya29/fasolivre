@extends('front.layouts.app')

@section('title', $book->title . ' – Fasolivre')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
        --faso-dark: #3E3E3E;
    }

    .glass {
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        background: rgba(255,255,255,.78);
        border: 1px solid rgba(255,255,255,.55);
    }

    .clamp-2{
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@php
    $user = auth()->user();

    $category = $book->category;
    $coverUrl = $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');

    // états d'accès
    $hasPurchase = false;
    $hasPendingPurchase = false;
    $hasActiveSub = false;

    if ($user) {
        $purchase = \App\Models\BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        $hasPurchase = $purchase && !is_null($purchase->purchased_at);
        $hasPendingPurchase = $purchase && is_null($purchase->purchased_at);

        $hasActiveSub = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();
    }

    $isFree = $book->access_type === 'free';
    $isPaid = $book->access_type === 'paid';
    $isSub  = $book->access_type === 'subscription';

    $canRead =
        $isFree
        || ($isPaid && $hasPurchase)
        || ($isSub && $hasActiveSub)
        || $hasPurchase; // achat autorisé même si subscription

    $loginUrl = route('login', ['redirect' => url()->current()]);

    $priceLabel = $isFree ? 'Gratuit' : ($isPaid ? number_format($book->price,0,',',' ').' FCFA' : 'Abonnement');
@endphp

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- HERO TOP (breadcrumb + titre) --}}
    <div class="mb-8">
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('books.index') }}" class="hover:text-[var(--faso-orange)] inline-flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Retour aux livres
            </a>

            @if($category)
                <span class="text-slate-300">/</span>
                <a href="{{ route('categories.show', $category->slug) }}" class="hover:text-[var(--faso-orange)]">
                    {{ $category->name }}
                </a>
            @endif
        </div>

        <div class="mt-4 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div class="space-y-2">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $book->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                    </span>

                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        Publié le {{ optional($book->published_at)->format('d M Y') ?? '—' }}
                    </span>

                    @if($category)
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100">
                            <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                            {{ $category->name }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- mini badge prix en haut (desktop) --}}
            <div class="hidden lg:flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-[12px] font-extrabold shadow-sm
                             {{ $isFree ? 'bg-emerald-500 text-white' : 'bg-white border border-slate-200 text-slate-900' }}">
                    <i data-lucide="{{ $isFree ? 'gift' : ($isPaid ? 'wallet' : 'crown') }}"
                       class="w-4 h-4 {{ $isFree ? '' : ($isPaid ? 'text-[var(--faso-orange)]' : 'text-indigo-600') }}"></i>
                    {{ $priceLabel }}
                </span>

                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-[12px] font-bold bg-slate-100 text-slate-700">
                    <i data-lucide="bookmark" class="w-4 h-4"></i>
                    {{ strtoupper($book->format ?? 'PDF') }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

        {{-- LEFT (cover + access card) --}}
        <div class="lg:col-span-4 space-y-5">

            {{-- Cover --}}
            <div class="relative overflow-hidden rounded-3xl shadow-xl border border-slate-100 bg-white">
                <img src="{{ $coverUrl }}"
                     alt="{{ $book->title }}"
                     class="w-full object-cover aspect-[3/4]">

                {{-- overlay bas avec prix --}}
                <div class="absolute inset-x-0 bottom-0 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl text-[12px] font-extrabold shadow-lg shadow-black/10
                                     {{ $isFree ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-900' }}
                                     backdrop-blur border border-white/60">
                            <i data-lucide="{{ $isFree ? 'gift' : ($isPaid ? 'wallet' : 'crown') }}"
                               class="w-4 h-4 {{ $isFree ? '' : ($isPaid ? 'text-[var(--faso-orange)]' : 'text-indigo-600') }}"></i>
                            {{ $priceLabel }}
                        </span>

                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl text-[12px] font-bold bg-black/45 text-white backdrop-blur">
                            <i data-lucide="file" class="w-4 h-4"></i>
                            {{ strtoupper($book->format ?? 'PDF') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Access / CTA card --}}
            <div class="glass rounded-3xl p-5 shadow-sm space-y-4">

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-extrabold text-slate-900">Accès & lecture</p>
                        <p class="text-xs text-slate-500 mt-0.5">Accès activé automatiquement après paiement confirmé.</p>
                    </div>

                    @if($isFree)
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-extrabold">FREE</span>
                    @elseif($isPaid)
                        <span class="px-2.5 py-1 rounded-full bg-orange-100 text-[var(--faso-orange)] text-[11px] font-extrabold">PAYANT</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 text-[11px] font-extrabold">ABONN.</span>
                    @endif
                </div>

                <div class="space-y-3">

                    @auth

                        @if($canRead)

                            @if($book->pdf_file)
                                <a href="{{ route('read.book', ['slug' => $book->slug]) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                          bg-[var(--faso-green)] text-white font-semibold
                                          hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                                    <i data-lucide="book-open" class="w-5 h-5"></i>
                                    Lire le livre (PDF)
                                </a>
                            @endif

                            @if($book->audio_file)
                                <a href="{{ route('read.audio', $book->slug) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                          bg-[var(--faso-orange)] text-white font-semibold
                                          hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                                    <i data-lucide="headphones" class="w-5 h-5"></i>
                                    Écouter l’audiobook
                                </a>
                            @endif

                            @if(!$book->pdf_file && !$book->audio_file)
                                <div class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-600 text-sm">
                                    Aucun fichier disponible pour ce livre.
                                </div>
                            @endif

                        @else

                            @if($isPaid)

                                @if($hasPendingPurchase)
                                    <div class="w-full px-5 py-3 rounded-2xl bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                                        Paiement en cours… Ton accès sera activé dès confirmation.
                                        <div class="text-xs mt-1 text-yellow-700">Rafraîchis la page dans quelques secondes.</div>
                                    </div>
                                @else
                                    <button type="button"
                                            data-book-id="{{ $book->id }}"
                                            class="pay-book-btn w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                                   bg-slate-900 text-white font-semibold
                                                   hover:bg-slate-800 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                                        Payer pour lire
                                    </button>
                                @endif

                            @elseif($isSub)

                                <a href="{{ route('plans.page') }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                          bg-indigo-600 text-white font-semibold
                                          hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                                    <i data-lucide="crown" class="w-5 h-5"></i>
                                    Voir les abonnements
                                </a>

                                @if($hasPendingPurchase)
                                    <div class="w-full px-5 py-3 rounded-2xl bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                                        Paiement en cours… Ton accès sera activé dès confirmation.
                                    </div>
                                @else
                                    <button type="button"
                                            data-book-id="{{ $book->id }}"
                                            class="pay-book-btn w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                                   bg-[var(--faso-orange)] text-white font-semibold
                                                   hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                                        Acheter ce livre
                                    </button>
                                @endif

                            @endif

                            <div class="text-xs text-slate-500 leading-relaxed">
                                Ton accès sera activé automatiquement après confirmation du paiement (webhook).
                            </div>

                        @endif

                    @else
                        <a href="{{ $loginUrl }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                  bg-slate-900 text-white font-semibold hover:bg-slate-800 hover:shadow-lg">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                            Se connecter pour continuer
                        </a>
                    @endauth
                </div>

                {{-- Formats --}}
                <div class="pt-2 border-t border-slate-200/60">
                    <p class="text-xs font-semibold text-slate-700 mb-2">Formats disponibles</p>
                    <div class="flex flex-wrap gap-2">
                        @if($book->pdf_file)
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold inline-flex items-center gap-1">
                                <i data-lucide="file-text" class="w-4 h-4"></i> PDF
                            </span>
                        @endif
                        @if($book->audio_file)
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold inline-flex items-center gap-1">
                                <i data-lucide="headphones" class="w-4 h-4"></i> Audio
                            </span>
                        @endif
                        @if(!$book->pdf_file && !$book->audio_file)
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">
                                —
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT (description + infos) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Description --}}
            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-slate-500">À propos du livre</p>
                        <h2 class="text-xl font-extrabold text-slate-900">Description</h2>
                    </div>

                    {{-- mini pill access --}}
                    <span class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[var(--faso-green)]"></i>
                        Accès sécurisé
                    </span>
                </div>

                <div class="mt-4 text-slate-700 text-sm leading-relaxed">
                    {!! nl2br(e($book->description)) !!}
                </div>
            </div>

            {{-- Infos rapides --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
                    <p class="text-xs text-slate-500 font-semibold">Auteur</p>
                    <p class="mt-1 font-extrabold text-slate-900">
                        {{ optional($book->author)->name ?? 'Auteur inconnu' }}
                    </p>
                </div>

                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
                    <p class="text-xs text-slate-500 font-semibold">Catégorie</p>
                    <p class="mt-1 font-extrabold text-slate-900">
                        {{ $category ? $category->name : '—' }}
                    </p>
                </div>

                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
                    <p class="text-xs text-slate-500 font-semibold">Accès</p>
                    <p class="mt-1 font-extrabold text-slate-900">
                        {{ $isFree ? 'Gratuit' : ($isPaid ? 'Payant' : 'Abonnement') }}
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================
         LIVRES SIMILAIRES — EN BAS (user friendly)
    ============================================================ --}}
    @if(isset($related) && $related->count())
        <section class="mt-16">
            <div class="flex items-center justify-between mb-6 gap-4">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                    Livres similaires
                </h2>

                @if($book->category)
                    <a href="{{ route('categories.show', $book->category->slug) }}"
                       class="text-sm font-semibold text-[var(--faso-orange)] hover:underline inline-flex items-center gap-1">
                        Voir la catégorie <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
                @foreach($related as $sim)
                    @php
                        $simCover = $sim->cover ? asset('storage/'.$sim->cover) : asset('images/placeholder-book.jpg');

                        $simFree = $sim->access_type === 'free';
                        $simPaid = $sim->access_type === 'paid';
                        $simSub  = $sim->access_type === 'subscription';

                        $simPrice = $simFree ? 'Gratuit' : ($simPaid ? number_format($sim->price,0,',',' ').' FCFA' : 'Abonnement');
                        $simIcon  = $simFree ? 'gift' : ($simPaid ? 'wallet' : 'crown');
                    @endphp

                    <a href="{{ route('books.show', $sim->slug) }}"
                       class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm
                              hover:shadow-xl hover:-translate-y-1 transition">

                        <div class="relative">
                            <img src="{{ $simCover }}" alt="{{ $sim->title }}"
                                 loading="lazy"
                                 class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">

                            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold
                                             {{ $simFree ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-900' }}
                                             backdrop-blur border border-white/60 shadow-lg shadow-black/10">
                                    <i data-lucide="{{ $simIcon }}"
                                       class="w-4 h-4 {{ $simFree ? '' : ($simPaid ? 'text-[var(--faso-orange)]' : 'text-indigo-600') }}"></i>
                                    {{ $simPrice }}
                                </span>

                                <span class="w-10 h-10 rounded-2xl bg-white/15 border border-white/25 backdrop-blur
                                             flex items-center justify-center text-white group-hover:bg-white/25 transition">
                                    <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                                </span>
                            </div>
                        </div>

                        <div class="p-4 space-y-2">
                            <h3 class="font-extrabold text-[13px] sm:text-sm text-slate-900 leading-snug clamp-2">
                                {{ $sim->title }}
                            </h3>

                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5 truncate">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                {{ optional($sim->author)->name ?? 'Auteur inconnu' }}
                            </p>
                        </div>

                        <div class="absolute inset-0 rounded-3xl ring-0 ring-[var(--faso-orange)]/0 group-hover:ring-2 group-hover:ring-[var(--faso-orange)]/20 transition pointer-events-none"></div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

</div>

{{-- JS Paiement livre --}}
@auth
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pay-book-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const bookId = btn.getAttribute('data-book-id');

            const original = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            btn.innerHTML = `<span class="inline-flex items-center gap-2">
                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"></circle>
                    <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="3"></path>
                </svg>
                Chargement...
            </span>`;

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
                btn.classList.remove('opacity-80', 'cursor-not-allowed');
                btn.innerHTML = original;
            }
        });
    });
});
</script>
@endauth

@endsection
