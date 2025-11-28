@extends('admin.layouts.app')

@section('title', 'Nouvelle Catégorie')

@section('content')

<div class="bg-white shadow-sm rounded-xl border p-6 max-w-xl">

    <h2 class="text-xl font-semibold mb-4">Nouvelle catégorie</h2>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="mb-5">
            <label class="block mb-1 text-gray-700 font-medium">Nom</label>
            <input type="text" name="name" class="w-full border p-2 rounded-lg" required>
        </div>

        <div class="flex gap-3">
            <button class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Enregistrer
            </button>

            <a href="{{ route('admin.categories.index') }}"
               class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>

</div>

@endsection
