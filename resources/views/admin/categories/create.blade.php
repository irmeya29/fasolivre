@extends('admin.layouts.app')

@section('title', 'Nouvelle Catégorie')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.categories.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Nouvelle Catégorie</h1>
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la catégorie <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required
                       class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors placeholder-gray-400"
                       placeholder="Ex: Science-Fiction">
                @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors placeholder-gray-400"
                          placeholder="Brève description de ce genre littéraire..."></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 shadow-sm transition-all">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                    <i data-feather="save" class="w-4 h-4"></i>
                    Créer la catégorie
                </button>
            </div>

        </div>
    </form>
</div>

<script>feather.replace();</script>
@endsection
