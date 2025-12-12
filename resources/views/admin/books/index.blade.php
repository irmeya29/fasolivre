@extends('admin.layouts.app')

@section('title', 'Gestion des Livres')

@section('content')

{{-- On initialise AlpineJS ici pour gérer l'état de la modale et l'URL à supprimer --}}
<div x-data="{
    showDeleteModal: false,
    deleteUrl: '',
    confirmDelete(url) {
        this.deleteUrl = url;
        this.showDeleteModal = true;
    }
}">

    {{-- EN-TÊTE & ACTIONS --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Bibliothèque</h2>
            <p class="text-sm text-gray-500 mt-1">Gérez votre catalogue, les prix et la publication.</p>
        </div>

        <a href="{{ route('admin.books.create') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Nouveau Livre
        </a>
    </div>

    {{-- BARRE DE FILTRES --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form action="{{ route('admin.books.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">

            {{-- Recherche --}}
            <div class="col-span-1 md:col-span-5 relative">
                <i data-feather="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher par titre, auteur..."
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            {{-- Filtre Catégorie --}}
            <div class="col-span-1 md:col-span-3">
                <select name="category" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre Status --}}
            <div class="col-span-1 md:col-span-2">
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
                    <option value="">Tous statuts</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publié</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                </select>
            </div>

            {{-- Boutons --}}
            <div class="col-span-1 md:col-span-2 flex gap-2 justify-end">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors shadow-sm">
                    <i data-feather="filter" class="w-4 h-4"></i>
                </button>

                @if(request()->anyFilled(['search', 'category', 'status']))
                    <a href="{{ route('admin.books.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 hover:text-red-600 transition-colors flex items-center justify-center shadow-sm" title="Réinitialiser">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLEAU DE DONNÉES --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold tracking-wide">
                        <th class="px-6 py-4 w-16">Cover</th>
                        <th class="px-6 py-4">Livre & Auteur</th>
                        <th class="px-6 py-4">Format</th>
                        <th class="px-6 py-4">Tarification</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($books as $b)
                    <tr class="hover:bg-gray-50/80 transition-colors">

                        {{-- Cover --}}
                        <td class="px-6 py-4 align-middle">
                            <div class="w-10 h-14 bg-gray-200 rounded shadow-sm overflow-hidden border border-gray-100">
                                @if($b->cover)
                                    <img src="{{ asset('storage/'.$b->cover) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-300">
                                        <i data-feather="image" class="w-4 h-4"></i>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Titre --}}
                        <td class="px-6 py-4 align-middle">
                            <div class="flex flex-col max-w-xs">
                                <span class="font-bold text-gray-800 text-sm truncate">{{ $b->title }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">{{ $b->author->name ?? 'Auteur inconnu' }}</span>
                                <span class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">{{ $b->category->name ?? 'Non classé' }}</span>
                            </div>
                        </td>

                        {{-- Format --}}
                        <td class="px-6 py-4 align-middle">
                            <div class="flex flex-col gap-1.5 items-start">
                                @if(Str::contains($b->format, 'pdf'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">
                                        <i data-feather="file-text" class="w-3 h-3 mr-1"></i> PDF
                                    </span>
                                @endif
                                @if(Str::contains($b->format, 'audio'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                        <i data-feather="headphones" class="w-3 h-3 mr-1"></i> AUDIO
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Prix --}}
                        <td class="px-6 py-4 align-middle">
                            @if($b->access_type === 'paid')
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 text-sm">
                                        {{ number_format($b->price, 0, ',', ' ') }} <span class="text-xs font-normal text-gray-500">FCFA</span>
                                    </span>
                                    <span class="text-[10px] text-indigo-600 font-medium">Achat unique</span>
                                </div>
                            @elseif($b->access_type === 'subscription')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <i data-feather="star" class="w-3 h-3 mr-1"></i> Premium
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    Gratuit
                                </span>
                            @endif
                        </td>

                        {{-- Statut --}}
                        <td class="px-6 py-4 align-middle">
                            @if($b->status === 'published')
                                <div class="flex items-center gap-1.5">
                                    <span class="relative flex h-2.5 w-2.5">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-xs font-medium text-emerald-700">En ligne</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-gray-300"></span>
                                    <span class="text-xs font-medium text-gray-500">Brouillon</span>
                                </div>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 align-middle text-right">
                            <div class="flex items-center justify-end gap-3">

                                {{-- Edit (Tooltip natif propre) --}}
                                <a href="{{ route('admin.books.edit', $b) }}"
                                   title="Modifier ce livre"
                                   class="flex items-center justify-center w-9 h-9 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-all">
                                    <i data-feather="edit-3" class="w-4 h-4"></i>
                                </a>

                                {{-- Delete (Déclenche la Modal) --}}
                                <button type="button"
                                        @click="confirmDelete('{{ route('admin.books.destroy', $b) }}')"
                                        title="Supprimer ce livre"
                                        class="flex items-center justify-center w-9 h-9 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition-all">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <div class="p-4 bg-gray-50 rounded-full mb-3 border border-gray-100">
                                    <i data-feather="book" class="w-8 h-8 opacity-50"></i>
                                </div>
                                <p class="text-base font-medium text-gray-600">Aucun livre trouvé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($books->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-500">
                <span class="font-bold text-gray-700">{{ $books->firstItem() }}</span> - <span class="font-bold text-gray-700">{{ $books->lastItem() }}</span> sur {{ $books->total() }}
            </p>
            <div>{{ $books->links() }}</div>
        </div>
        @endif
    </div>

    {{-- MODAL DE SUPPRESSION (AlpineJS) --}}
    <div x-show="showDeleteModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">

        {{-- Fond sombre (Backdrop) --}}
        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/75 transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            {{-- Contenu de la modal --}}
            <div x-show="showDeleteModal"
                 @click.away="showDeleteModal = false"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        {{-- Icône Attention Rouge --}}
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-feather="alert-triangle" class="h-6 w-6 text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Supprimer le livre</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer ce livre ? Cette action est irréversible et supprimera tous les fichiers associés.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    {{-- Formulaire de suppression dynamique --}}
                    <form :action="deleteUrl" method="POST" class="inline-flex w-full justify-center sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Supprimer définitivement
                        </button>
                    </form>

                    <button type="button" @click="showDeleteModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Script pour réinitialiser les icônes dans la modal dynamique --}}
<script>
    // Petit hack pour s'assurer que feather icons charge bien dans la modal si nécessaire
    document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({
            init() {
                feather.replace();
            }
        }))
    });
</script>

@endsection
