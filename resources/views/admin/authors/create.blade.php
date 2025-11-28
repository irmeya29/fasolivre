@extends('admin.layouts.app')

@section('title', 'Nouvel Auteur')

@section('content')

<div class="bg-white shadow-sm rounded-xl border p-6 max-w-3xl">

    <h2 class="text-xl font-semibold mb-4">Créer un auteur</h2>

    <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-5">
            <label class="block mb-1 text-gray-700 font-medium">Nom</label>
            <input type="text" name="name" class="w-full border p-2 rounded-lg" required>
        </div>

        <div class="mb-5">
            <label class="block mb-1 text-gray-700 font-medium">Biographie</label>
            <textarea name="bio" rows="4" class="w-full border p-2 rounded-lg"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block mb-1 text-gray-700 font-medium">Site web</label>
                <input type="url" name="website" class="w-full border p-2 rounded-lg">
            </div>

            <div>
                <label class="block mb-1 text-gray-700 font-medium">Facebook</label>
                <input type="url" name="facebook" class="w-full border p-2 rounded-lg">
            </div>

            <div>
                <label class="block mb-1 text-gray-700 font-medium">Instagram</label>
                <input type="url" name="instagram" class="w-full border p-2 rounded-lg">
            </div>

        </div>

        <div class="mb-5">
            <label class="block mb-1 text-gray-700 font-medium">Photo</label>
            <input type="file" name="photo" class="w-full border p-2 rounded-lg">
        </div>

        <div class="mb-5 flex items-center gap-2">
            <input type="checkbox" name="is_active" checked class="w-4 h-4">
            <span class="text-gray-700">Actif</span>
        </div>

        <div class="flex gap-3">
            <button class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Enregistrer
            </button>

            <a href="{{ route('admin.authors.index') }}"
               class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>

</div>

@endsection
