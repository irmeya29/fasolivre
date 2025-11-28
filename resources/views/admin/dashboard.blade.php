@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<h1 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard</h1>

{{-- CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">

    {{-- Books --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Livres</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $books }}</h3>
            </div>
            <div class="w-12 h-12 bg-indigo-100 flex items-center justify-center rounded-lg">
                <i data-feather="book-open" class="text-indigo-600"></i>
            </div>
        </div>
    </div>

    {{-- Authors --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Auteurs</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $authors }}</h3>
            </div>
            <div class="w-12 h-12 bg-yellow-100 flex items-center justify-center rounded-lg">
                <i data-feather="users" class="text-yellow-600"></i>
            </div>
        </div>
    </div>

    {{-- Categories --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Catégories</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $categories }}</h3>
            </div>
            <div class="w-12 h-12 bg-green-100 flex items-center justify-center rounded-lg">
                <i data-feather="tag" class="text-green-600"></i>
            </div>
        </div>
    </div>

    {{-- Submissions --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Soumissions en attente</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $pending_submissions }}</h3>
            </div>
            <div class="w-12 h-12 bg-red-100 flex items-center justify-center rounded-lg">
                <i data-feather="file-text" class="text-red-600"></i>
            </div>
        </div>
    </div>

</div>

{{-- GRIDS --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

    {{-- LAST BOOKS --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold mb-4">Derniers livres</h3>

        <ul class="divide-y">
            @forelse($latest_books as $b)
                <li class="py-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $b->title }}</p>
                        <span class="text-gray-500 text-sm">
                            {{ $b->author->name ?? 'Auteur inconnu' }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-400">{{ $b->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <p class="text-gray-500 text-sm">Aucun livre pour le moment.</p>
            @endforelse
        </ul>
    </div>

    {{-- LAST SUBMISSIONS --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold mb-4">Dernières soumissions</h3>

        <ul class="divide-y">
            @forelse($latest_submissions as $s)
                <li class="py-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $s->title }}</p>
                        <span class="text-gray-500 text-sm">
                            par {{ $s->user->name ?? 'Utilisateur supprimé' }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-400">{{ $s->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <p class="text-gray-500 text-sm">Aucune soumission.</p>
            @endforelse
        </ul>
    </div>

</div>

@endsection
