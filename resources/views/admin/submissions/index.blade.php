@extends('admin.layouts.app')

@section('title', 'Soumissions')

@section('content')

<div class="bg-white shadow-sm rounded-xl border p-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Soumissions</h2>

        {{-- FILTRES --}}
        <form method="GET" class="flex flex-col md:flex-row gap-2 md:items-center">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Rechercher (titre / nom / tel)"
                   class="px-3 py-2 border rounded-lg text-sm">

            <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">Tous les statuts</option>
                <option value="pending"  {{ request('status')==='pending' ? 'selected' : '' }}>En attente</option>
                <option value="accepted" {{ request('status')==='accepted' ? 'selected' : '' }}>Acceptée</option>
                <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Refusée</option>
            </select>

            <select name="category_id" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">Toutes catégories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string)request('category_id') === (string)$cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
                Filtrer
            </button>

            @if(request()->hasAny(['q','status','category_id']))
                <a href="{{ route('admin.submissions.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                <tr>
                    <th class="p-3">Utilisateur</th>
                    <th class="p-3">Catégorie</th>
                    <th class="p-3">Titre</th>
                    <th class="p-3">Téléphone</th>
                    <th class="p-3">Statut</th>
                    <th class="p-3">Date</th>
                    <th class="p-3 w-48">Actions</th>
                </tr>
            </thead>

            <tbody>
            @forelse($submissions as $s)
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-3 font-medium">
                        {{ $s->user->name ?? $s->full_name ?? 'Utilisateur supprimé' }}
                    </td>

                    <td class="p-3 text-gray-700">
                        {{ $s->category->name ?? '—' }}
                    </td>

                    <td class="p-3">{{ $s->title }}</td>

                    <td class="p-3 text-gray-700">
                        @php
                            $phone = trim(($s->phone_country_code ?? '') . ' ' . ($s->phone_number ?? ''));
                        @endphp
                        {{ $phone ?: '—' }}
                    </td>

                    <td class="p-3">
                        @if($s->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">En attente</span>
                        @elseif($s->status === 'accepted')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Acceptée</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Refusée</span>
                        @endif
                    </td>

                    <td class="p-3 text-gray-500">
                        {{ $s->created_at->format('d/m/Y') }}
                    </td>

                    <td class="p-3 flex flex-wrap gap-2">

                        <a href="{{ route('admin.submissions.show', $s) }}"
                           class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs">
                            Voir
                        </a>

                        <a href="{{ route('admin.submissions.edit', $s) }}"
                           class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs">
                            Statut
                        </a>

                        @if($s->pdf)
                            <a href="{{ route('admin.submissions.download', $s) }}"
                               class="px-3 py-1 bg-gray-100 text-gray-800 rounded-lg text-xs">
                                PDF
                            </a>
                        @endif

                        <form action="{{ route('admin.submissions.destroy', $s) }}"
                              method="POST"
                              onsubmit="return confirm('Supprimer cette soumission ?');">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs">
                                Supprimer
                            </button>
                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-5 text-center text-gray-500">
                        Aucune soumission pour le moment.
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>

</div>

@endsection
