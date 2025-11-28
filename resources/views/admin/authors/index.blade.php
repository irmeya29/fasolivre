@extends('admin.layouts.app')

@section('title', 'Auteurs')

@section('content')

<div class="bg-white shadow-sm rounded-xl border p-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Auteurs</h2>

        <a href="{{ route('admin.authors.create') }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            + Nouvel auteur
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Photo</th>
                    <th class="p-3">Nom</th>
                    <th class="p-3">Slug</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 w-40">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($authors as $a)
                    <tr class="border-b hover:bg-gray-50">

                        <td class="p-3">{{ $a->id }}</td>

                        <td class="p-3">
                            @if($a->photo)
                                <img src="{{ asset('storage/'.$a->photo) }}"
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                            @endif
                        </td>

                        <td class="p-3 font-medium">{{ $a->name }}</td>
                        <td class="p-3 text-gray-500">{{ $a->slug }}</td>

                        <td class="p-3">
                            @if($a->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-lg">
                                    Actif
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-lg">
                                    Inactif
                                </span>
                            @endif
                        </td>

                        <td class="p-3 flex gap-2">

                            <a href="{{ route('admin.authors.edit', $a) }}"
                               class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs">
                                Modifier
                            </a>

                            <form action="{{ route('admin.authors.destroy', $a) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer cet auteur ?');">
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
                        <td colspan="6" class="p-5 text-center text-gray-500">
                            Aucun auteur trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-4">
        {{ $authors->links() }}
    </div>

</div>

@endsection
