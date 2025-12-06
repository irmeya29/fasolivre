@extends('front.layouts.app')

@section('title', 'Recherche – Fasolivre')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
    }

    .glass {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .search-card:hover img {
        transform: scale(1.06);
    }

    .suggest-item:hover {
        background: #f8f8f8;
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-12">

    {{-- HEADER --}}
    <h1 class="text-3xl font-bold text-slate-900 mb-10 flex items-center gap-2">
        <i data-lucide="search" class="w-7 h-7 text-[var(--faso-orange)]"></i>
        Résultats de recherche
    </h1>


    {{-- SEARCH BAR + FILTERS --}}
    <div class="glass p-6 rounded-3xl shadow-lg border border-white/40 mb-12">

        {{-- FORM --}}
        <form action="{{ route('search') }}" method="GET" class="space-y-6">

            {{-- Search Input --}}
            <div class="relative">

                <input type="text"
                       name="q"
                       id="searchInput"
                       value="{{ $q }}"
                       placeholder="Rechercher un livre, un auteur, une catégorie..."
                       class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-[var(--faso-orange)] focus:border-transparent">

                <i data-lucide="search"
                   class="w-5 h-5 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2"></i>

                {{-- LIVE SUGGESTIONS --}}
                <div id="searchSuggestions"
                     class="absolute top-full left-0 w-full bg-white rounded-2xl border border-slate-200 shadow-xl hidden z-30 overflow-hidden"></div>
            </div>


            {{-- FILTERS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <select name="category" class="px-4 py-3 border rounded-xl text-sm" onchange="this.form.submit()">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="access" class="px-4 py-3 border rounded-xl text-sm" onchange="this.form.submit()">
                    <option value="">Type d'accès</option>
                    <option value="free" @selected(request('access')=='free')>Gratuit</option>
                    <option value="paid" @selected(request('access')=='paid')>Payant</option>
                    <option value="subscription" @selected(request('access')=='subscription')>Abonnement</option>
                </select>

                <select name="format" class="px-4 py-3 border rounded-xl text-sm" onchange="this.form.submit()">
                    <option value="">Format</option>
                    <option value="pdf" @selected(request('format')=='pdf')>PDF</option>
                    <option value="audio" @selected(request('format')=='audio')>Audio</option>
                    <option value="pdf_audio" @selected(request('format')=='pdf_audio')>PDF + Audio</option>
                </select>

                <select name="sort" class="px-4 py-3 border rounded-xl text-sm" onchange="this.form.submit()">
                    <option value="recent" @selected(request('sort')=='recent')>Les plus récents</option>
                    <option value="oldest" @selected(request('sort')=='oldest')>Les plus anciens</option>
                    <option value="price_asc" @selected(request('sort')=='price_asc')>Prix croissant</option>
                    <option value="price_desc" @selected(request('sort')=='price_desc')>Prix décroissant</option>
                </select>

            </div>

        </form>

    </div>


    {{-- ========================
        AUTEURS
    ========================= --}}
    @if($authors->count())
    <h2 class="text-xl font-semibold text-slate-900 mb-6 flex items-center gap-2">
        <i data-lucide="users" class="w-5 h-5 text-[var(--faso-green)]"></i>
        Auteurs trouvés ({{ $authors->count() }})
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8 mb-12">
        @foreach($authors as $author)
        <a href="{{ route('authors.show', $author->slug) }}"
           class="glass rounded-2xl p-5 text-center shadow hover:shadow-xl transition">

            <img src="{{ $author->photo ? asset('storage/'.$author->photo) : 'https://ui-avatars.com/api/?name='.urlencode($author->name) }}"
                 class="w-20 h-20 rounded-full mx-auto object-cover shadow">

            <p class="mt-4 text-sm font-semibold text-slate-900">{{ $author->name }}</p>
        </a>
        @endforeach
    </div>
    @endif



    {{-- ========================
        LIVRES
    ========================= --}}
    <h2 class="text-xl font-semibold text-slate-900 mb-6 flex items-center gap-2">
        <i data-lucide="book-open" class="w-5 h-5 text-[var(--faso-orange)]"></i>
        Livres trouvés ({{ $books->total() }})
    </h2>

    @if($books->count() == 0)
        <p class="text-slate-500">Aucun livre trouvé.</p>
    @else

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8">
        @foreach($books as $book)
        <a href="{{ route('books.show', $book->slug) }}"
           class="glass rounded-2xl overflow-hidden shadow-lg search-card transition">

            <div class="relative">
                <img src="{{ asset('storage/'.$book->cover) }}"
                     class="w-full h-56 object-cover transition duration-300">

                {{-- BADGES --}}
                @if($book->access_type == 'free')
                <span class="absolute top-2 left-2 px-2 py-1 text-[10px] bg-green-100 text-[var(--faso-green)] rounded-lg">
                    GRATUIT
                </span>
                @elseif($book->access_type == 'paid')
                <span class="absolute top-2 left-2 px-2 py-1 text-[10px] bg-orange-100 text-[var(--faso-orange)] rounded-lg">
                    PAYANT
                </span>
                @endif
            </div>

            <div class="p-4">
                <h3 class="font-medium text-sm truncate">{{ $book->title }}</h3>
                <p class="text-xs text-slate-500 mt-1 truncate flex items-center gap-1">
                    <i data-lucide="user" class="w-3 h-3"></i>
                    {{ $book->author->name ?? 'Auteur inconnu' }}
                </p>
            </div>

        </a>
        @endforeach
    </div>


    <div class="mt-12">
        {{ $books->links('pagination::tailwind') }}
    </div>

    @endif

</div>

@endsection


@section('scripts')
<script>
/* -----------------------------
   AJAX LIVE SEARCH
------------------------------ */
document.getElementById('searchInput').addEventListener('keyup', function () {

    let q = this.value.trim();
    let box = document.getElementById('searchSuggestions');

    if (q.length < 2) {
        box.classList.add('hidden');
        return;
    }

    fetch("{{ route('search.ajax') }}?q=" + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {

            if (data.length === 0) {
                box.innerHTML = '<div class="p-4 text-sm text-slate-500">Aucun résultat</div>';
            } else {
                box.innerHTML = data.map(book => `
                    <a href="/books/${book.slug}"
                       class="flex items-center gap-3 p-3 border-b last:border-0 suggest-item transition">

                        <img src="/storage/${book.cover}" class="w-10 h-14 rounded object-cover">

                        <div>
                            <p class="text-sm font-medium text-slate-800">${book.title}</p>
                            <p class="text-xs text-slate-500">${book.author ?? ''}</p>
                        </div>
                    </a>
                `).join('');
            }

            box.classList.remove('hidden');
        });
});
</script>
@endsection
