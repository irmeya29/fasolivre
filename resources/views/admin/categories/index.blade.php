@extends('admin.layouts.app')

@section('title', 'Catégories')
@section('page-title', 'Catégories')

@section('content')

<div class="bg-white shadow-sm rounded-xl border p-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Catégories</h2>

        <a href="{{ route('admin.categories.create') }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            + Nouvelle catégorie
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700 font-semibold border-b">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Nom</th>
                    <th class="p-3">Slug</th>
                    <th class="p-3 w-40">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($categories as $c)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $c->id }}</td>
                        <td class="p-3 font-medium">{{ $c->name }}</td>
                        <td class="p-3 text-gray-500">{{ $c->slug }}</td>
                        <td class="p-3 flex gap-2">

                            <a href="{{ route('admin.categories.edit', $c) }}"
                               class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs">
                                Modifier
                            </a>

                            <form action="{{ route('admin.categories.destroy', $c) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer ?');">
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
                        <td colspan="4" class="p-5 text-center text-gray-500">
                            Aucune catégorie disponible.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>

</div>

@endsection
