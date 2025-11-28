@extends('admin.layouts.app')

@section('title', 'Livres')

@section('content')

<div class="bg-white shadow-sm rounded-xl border p-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Livres</h2>

        <a href="{{ route('admin.books.create') }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            + Nouveau livre
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                <tr>
                    <th class="p-3">Cover</th>
                    <th class="p-3">Titre</th>
                    <th class="p-3">Auteur</th>
                    <th class="p-3">Accès</th>
                    <th class="p-3">Prix</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 w-40">Actions</th>
                </tr>
            </thead>

            <tbody>
            @forelse($books as $b)
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-3">
                        @if($b->cover)
                            <img src="{{ asset('storage/'.$b->cover) }}"
                                 class="w-12 h-16 object-cover rounded-lg shadow">
                        @else
                            <div class="w-12 h-16 bg-gray-200 rounded-lg"></div>
                        @endif
                    </td>

                    <td class="p-3 font-medium">{{ $b->title }}</td>
                    <td class="p-3">{{ $b->author->name ?? '—' }}</td>

                    <td class="p-3">
                        @if($b->access_type === 'free')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Gratuit</span>
                        @elseif($b->access_type === 'paid')
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">Payant</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">Abonnement</span>
                        @endif
                    </td>

                    <td class="p-3">
                        @if($b->access_type === 'paid')
                            {{ number_format($b->price, 0, ',', ' ') }} FCFA
                        @else
                            —
                        @endif
                    </td>

                    <td class="p-3">
                        @if($b->status === 'published')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Publié</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">Brouillon</span>
                        @endif
                    </td>

                    <td class="p-3 flex gap-2">

                        <a href="{{ route('admin.books.edit', $b) }}"
                           class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs">
                            Modifier
                        </a>

                        <form action="{{ route('admin.books.destroy', $b) }}"
                              method="POST"
                              onsubmit="return confirm('Supprimer ce livre ?');">
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
                        Aucun livre pour le moment.
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-4">
        {{ $books->links() }}
    </div>

</div>

@endsection
