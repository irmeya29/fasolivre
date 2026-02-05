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
    /**
     * ✅ IMPORTANT :
     * - On ne considère PLUS "BookPurchase purchased_at NULL" comme "pending".
     * - On considère "pending" seulement si un Payment PENDING existe.
     *
     * ✅ Cette vue suppose que le controller show() passe:
     * $isFree, $isPaid, $isSub, $canRead, $loginUrl, $shouldAutoPay,
     * $pendingPayment, $failedPayment, $hasActiveSub, $hasPurchase
     *
     * Si tu n'as pas encore modifié le controller, fais-le (je te l'ai donné).
     */

    $user = auth()->user();
    $category = $book->category;
    $coverUrl = $book->cover ? asset('storage/'.$book->cover) : asset('images/placeholder-book.jpg');

    $priceLabel = $isFree ? 'Gratuit' : ($isPaid ? number_format($book->price,0,',',' ').' FCFA' : 'Abonnement');

    $hasPendingPurchase = !empty($pendingPayment);
    $hasFailedPurchase  = !empty($failedPayment);

    // ✅ si pending existe mais pas de checkout_url, on affiche quand même un message
    $pendingCheckoutUrl = $pendingPayment->checkout_url ?? null;
@endphp

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- breadcrumb --}}
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
                </div>
            </div>

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

        {{-- LEFT --}}
        <div class="lg:col-span-4 space-y-5">
            <div class="relative overflow-hidden rounded-3xl shadow-xl border border-slate-100 bg-white">
                <img src="{{ $coverUrl }}" alt="{{ $book->title }}" class="w-full object-cover aspect-[3/4]">
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

            <div class="glass rounded-3xl p-5 shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-extrabold text-slate-900">Accès & lecture</p>
                        <p class="text-xs text-slate-500 mt-0.5">Votre accès est activé après validation du paiement.</p>
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
                                          bg-[var(--faso-green)] text-white font-semibold hover:shadow-lg">
                                    <i data-lucide="book-open" class="w-5 h-5"></i>
                                    Lire le livre (PDF)
                                </a>
                            @endif

                            @if($book->audio_file)
                                <a href="{{ route('read.audio', $book->slug) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                          bg-[var(--faso-orange)] text-white font-semibold hover:shadow-lg">
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

                                {{-- ✅ PENDING réel (Payment.status = PENDING) --}}
                                @if($hasPendingPurchase)
                                    <div class="w-full px-5 py-3 rounded-2xl bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                                        Paiement en cours…
                                    </div>

                                    @if($pendingCheckoutUrl)
                                        <a href="{{ $pendingCheckoutUrl }}"
                                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                                  bg-slate-900 text-white font-semibold hover:bg-slate-800 hover:shadow-lg">
                                            <i data-lucide="external-link" class="w-5 h-5"></i>
                                            Reprendre le paiement
                                        </a>
                                    @else
                                        <button type="button" data-book-id="{{ $book->id }}"
                                            class="pay-book-btn w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                                   bg-slate-900 text-white font-semibold hover:bg-slate-800 hover:shadow-lg">
                                            <i data-lucide="credit-card" class="w-5 h-5"></i>
                                            Continuer
                                        </button>
                                    @endif

                                {{-- ✅ FAILED (affiche erreur + réessayer) --}}
                                @elseif($hasFailedPurchase)
                                    <div class="w-full px-5 py-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                                        Paiement échoué. Vous pouvez réessayer.
                                    </div>

                                    <button type="button" data-book-id="{{ $book->id }}"
                                        class="pay-book-btn w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                               bg-slate-900 text-white font-semibold hover:bg-slate-800 hover:shadow-lg">
                                        <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                                        Réessayer le paiement
                                    </button>

                                {{-- ✅ Aucun paiement en cours => bouton payer --}}
                                @else
                                    <button type="button" data-book-id="{{ $book->id }}"
                                        class="pay-book-btn w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                               bg-slate-900 text-white font-semibold hover:bg-slate-800 hover:shadow-lg">
                                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                                        Payer maintenant
                                    </button>
                                @endif

                            @elseif($isSub)
                                <a href="{{ route('plans.page') }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                          bg-indigo-600 text-white font-semibold hover:bg-indigo-700 hover:shadow-lg">
                                    <i data-lucide="crown" class="w-5 h-5"></i>
                                    Voir les abonnements
                                </a>
                            @endif
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
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">À propos du livre</p>
                <h2 class="text-xl font-extrabold text-slate-900 mt-1">Description</h2>
                <div class="mt-4 text-slate-700 text-sm leading-relaxed">
                    {!! nl2br(e($book->description)) !!}
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
                    <p class="text-xs text-slate-500 font-semibold">Auteur</p>
                    <p class="mt-1 font-extrabold text-slate-900">{{ optional($book->author)->name ?? 'Auteur inconnu' }}</p>
                </div>
                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
                    <p class="text-xs text-slate-500 font-semibold">Catégorie</p>
                    <p class="mt-1 font-extrabold text-slate-900">{{ $category ? $category->name : '—' }}</p>
                </div>
                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
                    <p class="text-xs text-slate-500 font-semibold">Accès</p>
                    <p class="mt-1 font-extrabold text-slate-900">{{ $isFree ? 'Gratuit' : ($isPaid ? 'Payant' : 'Abonnement') }}</p>
                </div>
            </div>

            @if(isset($related) && $related->count())
                <section class="mt-6">
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2 mb-4">
                        <i data-lucide="sparkles" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                        Livres similaires
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
                        @foreach($related as $sim)
                            @php
                                $simCover = $sim->cover ? asset('storage/'.$sim->cover) : asset('images/placeholder-book.jpg');
                            @endphp
                            <a href="{{ route('books.show', $sim->slug) }}"
                               class="group relative bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm
                                      hover:shadow-xl hover:-translate-y-1 transition">
                                <img src="{{ $simCover }}" alt="{{ $sim->title }}" class="w-full aspect-[3/4] object-cover">
                                <div class="p-4">
                                    <h3 class="font-extrabold text-[13px] sm:text-sm text-slate-900 leading-snug clamp-2">{{ $sim->title }}</h3>
                                    <p class="text-[11px] text-slate-500 truncate mt-1">{{ optional($sim->author)->name ?? 'Auteur inconnu' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>

@auth
<script>
async function startBookPayment(bookId) {
    const res = await fetch("{{ route('pay.book') }}", {
        method: "POST",
        credentials: "same-origin", // ✅ indispensable pour envoyer la session/cookies
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json",
        },
        body: JSON.stringify({ book_id: parseInt(bookId, 10) }),
    });

    // ✅ parse robuste (parfois Laravel renvoie HTML en prod)
    const text = await res.text();
    let data = {};
    try { data = JSON.parse(text); } catch (e) {}

    // ✅ gestion des codes HTTP
    if (!res.ok) {
        if (res.status === 401) {
            // session non reconnue -> login
            window.location.href = "{{ route('login', ['redirect' => url()->current(), 'autopay' => 1]) }}";
            return;
        }

        if (res.status === 419) {
            alert("Session expirée. Rafraîchis la page puis réessaie.");
            return;
        }

        console.error("PAY BOOK ERROR", res.status, text);
        alert((data.message ?? "Impossible de générer le paiement.") + " (HTTP " + res.status + ")");
        return;
    }

    if (data.checkout_url) {
        window.location.href = data.checkout_url;
        return;
    }

    alert(data.message ?? "Impossible de générer le paiement.");
}

document.addEventListener('DOMContentLoaded', () => {
    // click manual
    document.querySelectorAll('.pay-book-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const bookId = btn.getAttribute('data-book-id');
            btn.disabled = true;
            try { await startBookPayment(bookId); }
            catch(e){ alert("Erreur réseau. Réessaie."); }
            finally { btn.disabled = false; }
        });
    });

    // ✅ AUTO PAY after login
    const autoPay = {{ $shouldAutoPay ? 'true' : 'false' }};
    if (autoPay) {
        startBookPayment({{ $book->id }});

        // ✅ évite de relancer à chaque refresh/back
        const url = new URL(window.location.href);
        url.searchParams.delete('autopay');
        window.history.replaceState({}, '', url.toString());
    }
});
</script>

@endauth

@endsection
