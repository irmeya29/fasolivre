@extends('admin.layouts.app')

@section('title', 'Modifier le livre')

@section('content')

{{--
    INIT ALPINE AVEC LES DONNÉES EXISTANTES
    On injecte les valeurs de la BDD directement dans le state Alpine
--}}
<div class="max-w-6xl mx-auto" x-data="{
    format: '{{ old('format', $book->format) }}',
    accessType: '{{ old('access_type', $book->access_type) }}',
    coverPreview: '{{ $book->cover ? asset('storage/'.$book->cover) : null }}',
    updatePreview(event) {
        const file = event.target.files[0];
        if (file) {
            this.coverPreview = URL.createObjectURL(file);
        }
    }
}">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.books.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Modifier : {{ $book->title }}</h1>
                <p class="text-sm text-gray-500">Mettez à jour les informations et les fichiers.</p>
            </div>
        </div>
        <div class="flex gap-3">
            {{-- Bouton Voir (Optionnel, si tu as un front) --}}
            {{-- <a href="#" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 shadow-sm transition-all">
                Voir en ligne
            </a> --}}

            <button type="submit" form="editBookForm" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                <i data-feather="save" class="w-4 h-4"></i>
                Enregistrer les modifications
            </button>
        </div>
    </div>

    <form id="editBookForm" action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- COLONNE GAUCHE (Contenu) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARTE 1 : INFO DE BASE --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-feather="info" class="w-4 h-4 text-indigo-500"></i>
                        Informations Générales
                    </h2>

                    <div class="space-y-5">
                        {{-- Titre --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre du livre <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" required value="{{ old('title', $book->title) }}"
                                class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors">
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Synopsis / Description</label>
                            <textarea name="description" id="description" rows="5"
                                class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors">{{ old('description', $book->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- CARTE 2 : DÉTAILS & FICHIERS --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-feather="file-text" class="w-4 h-4 text-indigo-500"></i>
                        Contenu & Fichiers
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Auteur --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Auteur <span class="text-red-500">*</span></label>
                            <select name="author_id" required class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-white">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" {{ $book->author_id == $author->id ? 'selected' : '' }}>
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Catégorie --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <select name="category_id" class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-white">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $book->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- SÉLECTEUR DE FORMAT --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format du livre</label>
                        <div class="grid grid-cols-3 gap-3">
                            {{-- Option PDF --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="format" value="pdf" x-model="format" class="peer sr-only">
                                <div class="rounded-lg border border-gray-200 p-3 text-center hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                    <span class="block text-sm font-medium">PDF</span>
                                </div>
                            </label>
                            {{-- Option Audio --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="format" value="audio" x-model="format" class="peer sr-only">
                                <div class="rounded-lg border border-gray-200 p-3 text-center hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                    <span class="block text-sm font-medium">Audio</span>
                                </div>
                            </label>
                            {{-- Option Mixte --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="format" value="pdf_audio" x-model="format" class="peer sr-only">
                                <div class="rounded-lg border border-gray-200 p-3 text-center hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                    <span class="block text-sm font-medium">PDF + Audio</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ZONES D'UPLOAD CONDITIONNELLES --}}
                    <div class="space-y-4 bg-gray-50 rounded-lg p-4 border border-dashed border-gray-300">

                        {{-- Upload PDF --}}
                        <div x-show="format === 'pdf' || format === 'pdf_audio'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fichier PDF</label>

                            {{-- Affichage du fichier actuel --}}
                            @if($book->pdf_file)
                                <div class="flex items-center gap-2 mb-2 px-3 py-2 bg-white border border-green-200 rounded-md text-sm text-green-700">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                    <span class="truncate">Actuel : {{ basename($book->pdf_file) }}</span>
                                </div>
                            @endif

                            <input type="file" name="pdf_file" accept="application/pdf"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all">
                        </div>

                        {{-- Separator --}}
                        <div x-show="format === 'pdf_audio'" class="border-t border-gray-200"></div>

                        {{-- Upload Audio --}}
                        <div x-show="format === 'audio' || format === 'pdf_audio'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fichier Audio (MP3)</label>

                            {{-- Affichage du fichier actuel --}}
                            @if($book->audio_file)
                                <div class="flex items-center gap-2 mb-2 px-3 py-2 bg-white border border-purple-200 rounded-md text-sm text-purple-700">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                    <span class="truncate">Actuel : {{ basename($book->audio_file) }}</span>
                                </div>
                            @endif

                            <input type="file" name="audio_file" accept="audio/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition-all">
                        </div>
                    </div>

                </div>
            </div>

            {{-- COLONNE DROITE (Settings) --}}
            <div class="space-y-6">

                {{-- CARTE 3 : COUVERTURE --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Couverture</h2>

                    <div class="relative group">
                        <div class="aspect-[2/3] w-full bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:border-indigo-400 transition-colors relative">

                            {{-- Image Preview (Alpine gère l'ancienne et la nouvelle) --}}
                            <template x-if="coverPreview">
                                <img :src="coverPreview" class="absolute inset-0 w-full h-full object-cover">
                            </template>

                            {{-- Placeholder si pas d'image --}}
                            <template x-if="!coverPreview">
                                <div class="text-center p-4">
                                    <i data-feather="image" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                    <p class="text-xs text-gray-500">Ajouter une couverture</p>
                                </div>
                            </template>

                            {{-- Input --}}
                            <input type="file" name="cover" accept="image/*" @change="updatePreview"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                </div>

                {{-- CARTE 4 : TARIFICATION --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Accès & Prix</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type d'accès</label>
                            <select name="access_type" x-model="accessType" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <option value="free">Gratuit</option>
                                <option value="paid">Payant (Achat unique)</option>
                                <option value="subscription">Abonnement (Premium)</option>
                            </select>
                        </div>

                        {{-- Champ Prix --}}
                        <div x-show="accessType === 'paid'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix de vente</label>
                            <div class="relative rounded-md shadow-sm">
                                <input type="number" name="price" min="0" value="{{ old('price', $book->price) }}" class="w-full rounded-lg border-gray-300 border pl-3 pr-12 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 sm:text-sm">FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARTE 5 : PUBLICATION --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Publication</h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">État du livre</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="draft" {{ $book->status === 'draft' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Brouillon</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="published" {{ $book->status === 'published' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Publié</span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@endsection
