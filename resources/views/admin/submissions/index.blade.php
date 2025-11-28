@extends('admin.layouts.app')

@section('title', 'Soumissions')

@section('content')

<div class="bg-white shadow-sm rounded-xl border p-6">

    <h2 class="text-xl font-semibold text-gray-800 mb-4">Soumissions</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                <tr>
                    <th class="p-3">Utilisateur</th>
                    <th class="p-3">Titre</th>
                    <th class="p-3">Statut</th>
                    <th class="p-3">Date</th>
                    <th class="p-3 w-40">Actions</th>
                </tr>
            </thead>

            <tbody>
            @forelse($submissions as $s)
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-3 font-medium">
                        {{ $s->user->name ?? 'Utilisateur supprimé' }}
                    </td>

                    <td class="p-3">{{ $s->title }}</td>

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

                    <td class="p-3 flex gap-2">

                        <a href="{{ route('admin.submissions.show', $s) }}"
                           class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs">
                            Voir
                        </a>

                        <a href="{{ route('admin.submissions.edit', $s) }}"
                           class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs">
                            Statut
                        </a>

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
                    <td colspan="5" class="p-5 text-center text-gray-500">
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
