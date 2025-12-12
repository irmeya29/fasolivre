@extends('admin.layouts.app')

@section('title', 'Tableau de Bord')

@section('content')

{{-- EN-TÊTE DE BIENVENUE --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Vue d'ensemble</h2>
        <p class="mt-1 text-sm text-gray-500">
            Bienvenue sur Fasolivre. Voici ce qu'il se passe aujourd'hui.
        </p>
    </div>
    <div class="mt-4 sm:mt-0 flex items-center gap-3">
        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
            {{ now()->isoFormat('D MMMM YYYY') }}
        </span>
    </div>
</div>

{{-- STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    {{-- Card: Livres --}}
    <a href="{{ route('admin.books.index') }}" class="group relative overflow-hidden bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md hover:ring-indigo-500/30">
        <dt>
            <div class="absolute rounded-md bg-indigo-50 p-3">
                <i data-feather="book" class="h-6 w-6 text-indigo-600"></i>
            </div>
            <p class="ml-16 truncate text-sm font-medium text-gray-500">Livres publiés</p>
        </dt>
        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
            <p class="text-2xl font-semibold text-gray-900">{{ $books }}</p>
        </dd>
        {{-- Décoration visuelle --}}
        <div class="absolute bottom-0 left-0 h-1 w-full bg-indigo-50 group-hover:bg-indigo-500 transition-colors duration-300"></div>
    </a>

    {{-- Card: Auteurs --}}
    <a href="{{ route('admin.authors.index') }}" class="group relative overflow-hidden bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md hover:ring-emerald-500/30">
        <dt>
            <div class="absolute rounded-md bg-emerald-50 p-3">
                <i data-feather="users" class="h-6 w-6 text-emerald-600"></i>
            </div>
            <p class="ml-16 truncate text-sm font-medium text-gray-500">Auteurs actifs</p>
        </dt>
        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
            <p class="text-2xl font-semibold text-gray-900">{{ $authors }}</p>
        </dd>
        <div class="absolute bottom-0 left-0 h-1 w-full bg-emerald-50 group-hover:bg-emerald-500 transition-colors duration-300"></div>
    </a>

    {{-- Card: Catégories --}}
    <a href="{{ route('admin.categories.index') }}" class="group relative overflow-hidden bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md hover:ring-blue-500/30">
        <dt>
            <div class="absolute rounded-md bg-blue-50 p-3">
                <i data-feather="tag" class="h-6 w-6 text-blue-600"></i>
            </div>
            <p class="ml-16 truncate text-sm font-medium text-gray-500">Catégories</p>
        </dt>
        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
            <p class="text-2xl font-semibold text-gray-900">{{ $categories }}</p>
        </dd>
        <div class="absolute bottom-0 left-0 h-1 w-full bg-blue-50 group-hover:bg-blue-500 transition-colors duration-300"></div>
    </a>

    {{-- Card: Soumissions --}}
    <a href="{{ route('admin.submissions.index') }}" class="group relative overflow-hidden bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md hover:ring-amber-500/30">
        <dt>
            <div class="absolute rounded-md bg-amber-50 p-3">
                <i data-feather="inbox" class="h-6 w-6 text-amber-600"></i>
            </div>
            <p class="ml-16 truncate text-sm font-medium text-gray-500">En attente</p>
        </dt>
        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
            <p class="text-2xl font-semibold text-gray-900">{{ $pending_submissions }}</p>
            @if($pending_submissions > 0)
                <span class="ml-2 text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Action requise</span>
            @endif
        </dd>
        <div class="absolute bottom-0 left-0 h-1 w-full bg-amber-50 group-hover:bg-amber-500 transition-colors duration-300"></div>
    </a>

</div>

{{-- GRIDS CONTENU --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

    {{-- TABLEAU : DERNIERS LIVRES --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden flex flex-col h-full">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Derniers ajouts</h3>
            <a href="{{ route('admin.books.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 flex items-center gap-1 transition-colors">
                Voir tout <i data-feather="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="overflow-x-auto flex-1">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-3 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Livre</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Auteur</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($latest_books as $book)
                        <tr class="group hover:bg-gray-50 transition-colors">
                            <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                <div class="flex items-center">
                                    {{-- Mini Cover --}}
                                    <div class="h-10 w-8 flex-shrink-0 rounded bg-gray-200 border border-gray-200 overflow-hidden">
                                        @if($book->cover)
                                            <img src="{{ asset('storage/'.$book->cover) }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                <i data-feather="image" class="w-3 h-3"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $book->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $book->category->name ?? 'Non classé' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ $book->author->name ?? 'Inconnu' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-400 text-right">
                                {{ $book->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                                <div class="mx-auto h-12 w-12 text-gray-300">
                                    <i data-feather="book-open" class="w-full h-full"></i>
                                </div>
                                <p class="mt-2 text-sm font-medium">Aucun livre pour le moment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- LISTE : SOUMISSIONS --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden flex flex-col h-full">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Soumissions récentes</h3>
                @if($pending_submissions > 0)
                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">{{ $pending_submissions }} nouvelles</span>
                @endif
            </div>
            <a href="{{ route('admin.submissions.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 flex items-center gap-1 transition-colors">
                Gérer <i data-feather="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="flex-1 overflow-y-auto">
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($latest_submissions as $sub)
                    <li class="relative flex items-center justify-between gap-x-6 px-6 py-5 hover:bg-gray-50 transition-colors">
                        <div class="flex min-w-0 gap-x-4">
                            {{-- Avatar Utilisateur --}}
                            <div class="h-10 w-10 flex-none rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs ring-1 ring-gray-200">
                                {{ substr($sub->user->name ?? 'U', 0, 2) }}
                            </div>
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold leading-6 text-gray-900">
                                    <a href="#" class="focus:outline-none">
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                        {{ $sub->title }}
                                    </a>
                                </p>
                                <p class="mt-1 flex text-xs leading-5 text-gray-500 truncate">
                                    Soumis par {{ $sub->user->name ?? 'Utilisateur inconnu' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <p class="text-xs leading-5 text-gray-500">{{ $sub->created_at->diffForHumans() }}</p>
                            <div class="mt-1 flex items-center gap-x-1.5">
                                @if($sub->status === 'pending')
                                    <div class="flex-none rounded-full bg-amber-500/20 p-1">
                                        <div class="h-1.5 w-1.5 rounded-full bg-amber-500"></div>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-500">En attente</p>
                                @else
                                    <div class="flex-none rounded-full bg-emerald-500/20 p-1">
                                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-500">Traité</p>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="py-10 text-center">
                        <div class="mx-auto h-12 w-12 text-gray-300">
                            <i data-feather="inbox" class="w-full h-full"></i>
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-500">Aucune soumission en attente.</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

</div>

@endsection
