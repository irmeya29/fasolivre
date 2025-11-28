@extends('front.layouts.app')

@section('title', 'Recherche – Fasolivre')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- HEADER --}}
    <h1 class="text-2xl font-semibold text-slate-900 mb-6 flex items-center gap-2">
        <i data-lucide="search" class="w-6 h-6 text-indigo-600"></i>
        Recherche
    </h1>

    {{-- FORM + SEARCH LIVE --}}
    <div class="relative mb-10">

        <form action="{{ route('search') }}" method="GET" class="flex flex-col lg:flex-row items-start lg:items-center gap-4">

            {{-- Input --}}
            <div class="relative w-full">
                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       id="searchInput"
                       placeholder="Rechercher un titre, mot clé ou auteur…"
                       class="w-full px-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">

                {{-- Suggestions AJAX --}}
                <div id="searchSuggestions"
                     class="absolute top-full left-0 w-full bg-white border rounded-xl shadow-lg hidden z-40"></div>
            </div>

            {{-- BUTTON --}}
            <button class="px-5 py-3 bg-indigo-600 text-white rounded-xl text-sm flex items-center gap-2">
                <i data-lucide="search"></i> Rechercher
            </button>
        </form>


        {{-- FILTERS --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Catégorie --}}
            <select name="category" onchange="this.form.submit()"
                class="px-4 py-3 border rounded-xl text-sm">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            {{-- Type d’accès --}}
            <select name="access" onchange="this.form.submit()"
                class="px-4 py-3 border rounded-xl text-sm">
                <option value="">Accès (tous)</option>
                <option value="free" @selected(request('access')=='free')>Gratuit</option>
                <option value="paid" @selected(request('access')=='paid')>Payant</option>
                <option value="subscription" @selected(request('access')=='subscription')>Abonnement</option>
            </select>

            {{-- Format --}}
            <select name="format" onchange="this.form.submit()"
                class="px-4 py-3 border rounded-xl text-sm">
                <option value="">Format (tous)</option>
                <option value="pdf" @selected(request('format')=='pdf')>PDF</option>
                <option value="audio" @selected(request('format')=='audio')>Audio</option>
                <option value="pdf_audio" @selected(request('format')=='pdf_audio')>PDF + Audio</option>
            </select>

            {{-- Tri --}}
            <select name="sort" onchange="this.form.submit()"
                class="px-4 py-3 border rounded-xl text-sm">
                <option value="recent" @selected(request('sort')=='recent')>Les plus récents</option>
                <option value="oldest" @selected(request('sort')=='oldest')>Les plus anciens</option>
                <option value="price_asc" @selected(request('sort')=='price_asc')>Prix croissant</option>
                <option value="price_desc" @selected(request('sort')=='price_desc')>Prix décroissant</option>
            </select>

        </div>

    </div>


    {{-- AUTEURS --}}
    @if($authors->count())
    <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
        <i data-lucide="users" class="w-5 h-5 text-indigo-600"></i>
        Auteurs trouvés
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6 mb-10">

        @foreach($authors as $author)
        <a href="{{ route('authors.show', $author->slug) }}"
           class="bg-white rounded-xl shadow p-4 text-center hover:shadow-lg transition">

            <img src="{{ $author->photo ? asset('storage/'.$author->photo) : 'https://ui-avatars.com/api/?name='.urlencode($author->name) }}"
                 class="w-20 h-20 rounded-full mx-auto object-cover">

            <p class="mt-3 text-sm font-semibold">{{ $author->name }}</p>
        </a>
        @endforeach
    </div>
    @endif


    {{-- LIVRES --}}
    <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
        <i data-lucide="book-open" class="w-5 h-5 text-indigo-600"></i>
        Livres trouvés
    </h2>

    @if($books->count() == 0)
        <p class="text-slate-500 text-sm">Aucun livre trouvé.</p>
    @else

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach($books as $book)
        <a href="{{ route('books.show', $book->slug) }}"
           class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

            <img src="{{ asset('storage/'.$book->cover) }}"
                 class="w-full h-56 object-cover">

            <div class="p-3">
                <h3 class="font-medium text-sm">{{ Str::limit($book->title, 40) }}</h3>

                <p class="text-xs text-gray-500 flex items-center gap-1">
                    <i data-lucide="user" class="w-3 h-3"></i>
                    {{ $book->author->name ?? '' }}
                </p>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-10">
        {{ $books->links('pagination::tailwind') }}
    </div>

    @endif

</div>

@endsection


@section('scripts')
<script>
// ---------------------------
// AJAX LIVE SEARCH
// ---------------------------
document.getElementById('searchInput').addEventListener('keyup', function () {
    let q = this.value.trim();

    if (q.length < 2) {
        document.getElementById('searchSuggestions').classList.add('hidden');
        return;
    }

    fetch("{{ route('search.ajax') }}?q=" + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            let box = document.getElementById('searchSuggestions');

            if (data.length === 0) {
                box.innerHTML = '<div class="p-3 text-sm text-gray-500">Aucun résultat</div>';
            } else {
                box.innerHTML = data.map(book => `
                    <a href="/books/${book.slug}" class="flex items-center gap-3 p-3 hover:bg-gray-50">
                        <img src="/storage/${book.cover}" class="w-10 h-14 object-cover rounded">
                        <span class="text-sm">${book.title}</span>
                    </a>
                `).join('');
            }

            box.classList.remove('hidden');
        });
});
</script>
@endsection
