@extends('admin.layouts.app')

@section('title', 'Tableau de Bord')

@section('content')

{{-- SECTION STATS --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    {{-- Card 1 --}}
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-indigo-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Livres Totaux</p>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $books }}</h2>
        </div>
        <div class="p-3 bg-indigo-50 rounded-full text-indigo-600">
            <i data-feather="book-open" class="w-6 h-6"></i>
        </div>
    </div>

    {{-- Card 2 --}}
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Auteurs</p>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $authors }}</h2>
        </div>
        <div class="p-3 bg-emerald-50 rounded-full text-emerald-600">
            <i data-feather="users" class="w-6 h-6"></i>
        </div>
    </div>

    {{-- Card 3 --}}
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Catégories</p>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $categories }}</h2>
        </div>
        <div class="p-3 bg-blue-50 rounded-full text-blue-600">
            <i data-feather="tag" class="w-6 h-6"></i>
        </div>
    </div>

    {{-- Card 4 --}}
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">En Attente</p>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $pending_submissions }}</h2>
        </div>
        <div class="p-3 bg-amber-50 rounded-full text-amber-600">
            <i data-feather="clock" class="w-6 h-6"></i>
        </div>
    </div>

</div>

{{-- SECTION TABLEAUX --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

    {{-- DERNIERS LIVRES --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-semibold text-gray-800">Derniers Livres Ajoutés</h3>
            <a href="{{ route('admin.books.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium uppercase">Voir tout</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 font-medium">Titre</th>
                        <th class="px-6 py-3 font-medium">Auteur</th>
                        <th class="px-6 py-3 font-medium text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($latest_books as $book)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $book->title }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $book->author->name ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-400 text-right">{{ $book->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucune donnée disponible.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DERNIÈRES SOUMISSIONS --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-semibold text-gray-800">Dernières Soumissions</h3>
            <a href="{{ route('admin.submissions.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium uppercase">Gérer</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($latest_submissions as $sub)
                <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $sub->title }}</p>
                        <p class="text-xs text-gray-500">Par {{ $sub->user->name ?? 'Inconnu' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 text-xs rounded-full font-medium
                            {{ $sub->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $sub->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500 text-sm">
                    Aucune soumission en attente.
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
