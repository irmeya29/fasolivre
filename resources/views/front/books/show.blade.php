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

    $isFree = $book->access_type === 'free';
    $isPaid = $book->access_type === 'paid';
    $isSub  = $book->access_type === 'subscription';

    $hasPurchase = false;
    $hasPendingPurchase = false;
    $pendingCheckoutUrl = null;
    $hasActiveSub = false;

    if ($user) {
        $purchase = \App\Models\BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        $hasPurchase = $purchase && !is_null($purchase->purchased_at);
        $hasPendingPurchase = $purchase && is_null($purchase->purchased_at);

        if ($hasPendingPurchase) {
            $pendingPayment = \App\Models\Payment::where('payable_type', \App\Models\BookPurchase::class)
                ->where('payable_id', $purchase->id)
                ->whereIn('status', ['PENDING'])
                ->orderByDesc('id')
                ->first();

            $pendingCheckoutUrl = $pendingPayment?->checkout_url;
        }

        $hasActiveSub = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();
    }

    $canRead =
        $isFree
        || ($isPaid && $hasPurchase)
        || ($isSub && $hasActiveSub)
        || $hasPurchase;

    $priceLabel = $isFree ? 'Gratuit' : ($isPaid ? number_format($book->price,0,',',' ').' FCFA' : 'Abonnement');

    // ✅ Login revient ici + déclenche auto-paiement si livre payant
    $loginUrl = route('login', [
        'redirect' => url()->current(),
        'autopay'  => $isPaid ? 1 : 0,
        'book_id'  => $isPaid ? $book->id : null,
    ]);
@endphp

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- BREADCRUMB --}}
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

            <div class="hidden lg:flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-[12px] font-extrabold shadow-sm
                             {{ $isFree ? 'bg-emerald-500 text-white' : 'bg-white border border-slate-200 text-slate-900' }}">
                    <i data-lucide="{{ $isFree ? 'gift' : ($isPaid ? 'wallet' : 'crown') }}"
                       class="w-4 h-4 {{ $isFree ? '' : ($isPaid ? 'text-[var(--faso-orange)]' : 'text-indigo-600') }}"></i>
                    {{ $priceLabel }}
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
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl text-[12px] font-extrabold shadow-lg shadow-black/10
                                 {{ $isFree ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-900' }}
                                 backdrop-blur border border-white/60">
                        <i data-lucide="{{ $isFree ? 'gift' : ($isPaid ? 'wallet' : 'crown') }}"
                           class="w-4 h-4 {{ $isFree ? '' : ($isPaid ? 'text-[var(--faso-orange)]' : 'text-indigo-600') }}"></i>
                        {{ $priceLabel }}
                    </span>
                </div>
            </div>

            <div class="glass rounded-3xl p-5 shadow-sm space-y-4">

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-extrabold text-slate-900">Accès</p>
                        <p class="text-xs text-slate-500 mt-0.5">Lecture disponible selon ton accès.</p>
                    </div>
                </div>

                <div class="space-y-3">

                    @auth

                        {{-- ✅ si paiement en cours et checkout_url existe => bouton "Continuer le paiement" --}}
                        @if(!$canRead && $isPaid && $hasPendingPurchase && $pendingCheckoutUrl)
                            <a href="{{ $pendingCheckoutUrl }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                      bg-slate-900 text-white font-semibold hover:bg-slate-800 hover:shadow-lg">
                                <i data-lucide="external-link" class="w-5 h-5"></i>
                                Continuer le paiement
                            </a>
                        @endif

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

                        @else

                            @if($isPaid)
                                {{-- bouton payer --}}
                                @if(!$hasPendingPurchase)
                                    <button type="button" data-book-id="{{ $book->id }}"
                                            class="pay-book-btn w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                                   bg-slate-900 text-white font-semibold hover:bg-slate-800 hover:shadow-lg">
                                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                                        Acheter maintenant
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
                        {{-- ✅ Login + auto-pay après connexion si payant --}}
                        <a href="{{ $loginUrl }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl
                                  bg-slate-900 text-white font-semibold hover:bg-slate-800 hover:shadow-lg">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                            Se connecter
                        </a>
                    @endauth
                </div>

                <div class="pt-2 border-t border-slate-200/60">
                    <p class="text-xs font-semibold text-slate-700 mb-2">Formats</p>
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
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-8 space-y-6">

            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">À propos</p>
                <h2 class="text-xl font-extrabold text-slate-900 mt-1">Description</h2>

                <div class="mt-4 text-slate-700 text-sm leading-relaxed">
                    {!! nl2br(e($book->description)) !!}
                </div>
            </div>

        </div>
    </div>

    {{-- RELATED --}}
    @if(isset($related) && $related->count())
        <section class="mt-16">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 flex items-center gap-2 mb-6">
                <i data-lucide="sparkles" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                Livres similaires
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 lg:gap-7">
                @foreach($related as $sim)
                    @php
                        $simCover = $sim->cover ? asset('storage/'.$sim->cover) : asset('images/placeholder-book.jpg');
                    @endphp
                    <a href="{{ route('books.show', $sim->slug) }}"
                       class="group bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition">
                        <img src="{{ $simCover }}" alt="{{ $sim->title }}" loading="lazy"
                             class="w-full aspect-[3/4] object-cover group-hover:scale-[1.03] transition duration-300">
                        <div class="p-4">
                            <h3 class="font-extrabold text-[13px] sm:text-sm text-slate-900 leading-snug clamp-2">{{ $sim->title }}</h3>
                            <p class="text-[11px] text-slate-500 mt-2 truncate">
                                {{ optional($sim->author)->name ?? 'Auteur inconnu' }}
                            </p>
                        </div>
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
            btn.innerHTML = `Chargement...`;

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
