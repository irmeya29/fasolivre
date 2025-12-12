@extends('admin.layouts.app')

@section('title', 'Gestion des Auteurs')

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
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Auteurs</h2>
            <p class="text-sm text-gray-500 mt-1">Gérez les écrivains et leurs profils publics.</p>
        </div>

        <a href="{{ route('admin.authors.create') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Nouvel Auteur
        </a>
    </div>

    {{-- BARRE DE FILTRES --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form action="{{ route('admin.authors.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">

            {{-- Recherche --}}
            <div class="col-span-1 md:col-span-6 relative">
                <i data-feather="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher par nom, biographie..."
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            {{-- Filtre Status --}}
            <div class="col-span-1 md:col-span-4">
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs uniquement</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="col-span-1 md:col-span-2 flex gap-2 justify-end">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors shadow-sm">
                    <i data-feather="filter" class="w-4 h-4"></i>
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.authors.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 hover:text-red-600 transition-colors flex items-center justify-center shadow-sm" title="Réinitialiser">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold tracking-wide">
                        <th class="px-6 py-4 w-20">Profil</th>
                        <th class="px-6 py-4">Informations</th>
                        <th class="px-6 py-4 text-center">Publications</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($authors as $a)
                    <tr class="hover:bg-gray-50/80 transition-colors">

                        {{-- Avatar --}}
                        <td class="px-6 py-4 align-middle">
                            @if($a->photo)
                                <img src="{{ asset('storage/'.$a->photo) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white shadow-sm">
                            @else
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm ring-2 ring-white shadow-sm">
                                    {{ substr($a->name, 0, 1) }}
                                </div>
                            @endif
                        </td>

                        {{-- Infos & Social --}}
                        <td class="px-6 py-4 align-middle">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800 text-sm">{{ $a->name }}</span>
                                {{-- Réseaux sociaux (petites icônes) --}}
                                <div class="flex items-center gap-2 mt-1">
                                    @if($a->website)
                                        <a href="{{ $a->website }}" target="_blank" class="text-gray-400 hover:text-indigo-600 transition-colors" title="Site Web"><i data-feather="globe" class="w-3 h-3"></i></a>
                                    @endif
                                    @if($a->facebook)
                                        <a href="{{ $a->facebook }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition-colors" title="Facebook"><i data-feather="facebook" class="w-3 h-3"></i></a>
                                    @endif
                                    @if($a->instagram)
                                        <a href="{{ $a->instagram }}" target="_blank" class="text-gray-400 hover:text-pink-600 transition-colors" title="Instagram"><i data-feather="instagram" class="w-3 h-3"></i></a>
                                    @endif
                                    @if(!$a->website && !$a->facebook && !$a->instagram)
                                        <span class="text-[10px] text-gray-300 italic">Aucun lien social</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Nombre de livres --}}
                        <td class="px-6 py-4 align-middle text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $a->books_count }} livre(s)
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td class="px-6 py-4 align-middle">
                            @if($a->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Inactif
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 align-middle text-right">
                            <div class="flex items-center justify-end gap-3">

                                <a href="{{ route('admin.authors.edit', $a) }}"
                                   title="Modifier"
                                   class="flex items-center justify-center w-9 h-9 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-all">
                                    <i data-feather="edit-3" class="w-4 h-4"></i>
                                </a>

                                <button type="button"
                                        @click="confirmDelete('{{ route('admin.authors.destroy', $a) }}')"
                                        title="Supprimer"
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
                                    <i data-feather="users" class="w-8 h-8 opacity-50"></i>
                                </div>
                                <p class="text-base font-medium text-gray-600">Aucun auteur trouvé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($authors->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-500">
                Page <span class="font-bold text-gray-700">{{ $authors->currentPage() }}</span> sur {{ $authors->lastPage() }}
            </p>
            <div>{{ $authors->links() }}</div>
        </div>
        @endif
    </div>

    {{-- MODAL DE SUPPRESSION --}}
    <div x-show="showDeleteModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/75 transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
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
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-feather="alert-triangle" class="h-6 w-6 text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Supprimer cet auteur</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr ? Si vous supprimez cet auteur, ses livres risquent de ne plus avoir de propriétaire ou de devoir être supprimés également.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="deleteUrl" method="POST" class="inline-flex w-full justify-center sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Confirmer la suppression
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

{{-- Init Icons pour le contenu Ajax/Alpine --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({
            init() { feather.replace(); }
        }))
    });
</script>

@endsection
