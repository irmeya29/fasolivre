@extends('admin.layouts.app')

@section('title', 'Gestion des Catégories')

@section('content')

<div x-data="{
    showDeleteModal: false,
    deleteUrl: '',
    confirmDelete(url) {
        this.deleteUrl = url;
        this.showDeleteModal = true;
    }
}">

    {{-- EN-TÊTE --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Catégories</h2>
            <p class="text-sm text-gray-500 mt-1">Organisez les genres littéraires de votre plateforme.</p>
        </div>

        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Nouvelle Catégorie
        </a>
    </div>

    {{-- BARRE DE FILTRES --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">

            <div class="relative flex-1">
                <i data-feather="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher une catégorie..."
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors shadow-sm">
                <i data-feather="search" class="w-4 h-4"></i>
            </button>

            @if(request()->filled('search'))
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 hover:text-red-600 transition-colors flex items-center justify-center shadow-sm" title="Réinitialiser">
                    <i data-feather="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold tracking-wide">
                        <th class="px-6 py-4 w-16">#</th>
                        <th class="px-6 py-4">Nom & Description</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4 text-center">Livres associés</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $c)
                    <tr class="hover:bg-gray-50/80 transition-colors">

                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $c->id }}
                        </td>

                        <td class="px-6 py-4 align-middle">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800 text-sm">{{ $c->name }}</span>
                                <span class="text-xs text-gray-500 mt-1 truncate max-w-xs" title="{{ $c->description }}">
                                    {{ $c->description ?? 'Aucune description' }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 align-middle">
                            <code class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100">
                                {{ $c->slug }}
                            </code>
                        </td>

                        <td class="px-6 py-4 align-middle text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $c->books_count > 0 ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $c->books_count }} livre(s)
                            </span>
                        </td>

                        <td class="px-6 py-4 align-middle text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.categories.edit', $c) }}"
                                   class="flex items-center justify-center w-9 h-9 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-all">
                                    <i data-feather="edit-3" class="w-4 h-4"></i>
                                </a>

                                <button type="button"
                                        @click="confirmDelete('{{ route('admin.categories.destroy', $c) }}')"
                                        class="flex items-center justify-center w-9 h-9 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition-all">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <div class="p-4 bg-gray-50 rounded-full mb-3 border border-gray-100">
                                    <i data-feather="tag" class="w-8 h-8 opacity-50"></i>
                                </div>
                                <p class="text-base font-medium text-gray-600">Aucune catégorie trouvée</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-500">Pagination</p>
            <div>{{ $categories->links() }}</div>
        </div>
        @endif
    </div>

    {{-- MODAL --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="showDeleteModal" class="fixed inset-0 bg-gray-900/75 transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showDeleteModal" @click.away="showDeleteModal = false" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-feather="alert-triangle" class="h-6 w-6 text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Supprimer la catégorie</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Attention, assurez-vous qu'aucun livre n'est rattaché à cette catégorie avant de la supprimer.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="deleteUrl" method="POST" class="inline-flex w-full justify-center sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Supprimer</button>
                    </form>
                    <button type="button" @click="showDeleteModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({ init() { feather.replace(); } }))
    });
</script>

@endsection
