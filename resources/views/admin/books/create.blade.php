@extends('admin.layouts.app')

@section('title', 'Nouveau Livre')

@section('content')

{{-- Initialisation des données dynamiques avec Alpine.js --}}
<div class="max-w-6xl mx-auto" x-data="{
    format: 'pdf',
    accessType: 'free',
    coverPreview: null,
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
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Ajouter un livre</h1>
                <p class="text-sm text-gray-500">Remplissez les informations ci-dessous pour créer une nouvelle entrée.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" form="createBookForm" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                <i data-feather="save" class="w-4 h-4"></i>
                Enregistrer le livre
            </button>
        </div>
    </div>

    <form id="createBookForm" action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- COLONNE GAUCHE (Informations Principales) --}}
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
                            <input type="text" name="title" id="title" required
                                class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors placeholder-gray-400"
                                placeholder="Ex: Les misérables">
                            @error('title') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Synopsis / Description</label>
                            <textarea name="description" id="description" rows="5"
                                class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors placeholder-gray-400"
                                placeholder="Résumé captivant du livre..."></textarea>
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
                                <option value="">Sélectionner un auteur</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Catégorie --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <select name="category_id" class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-white">
                                <option value="">Sélectionner une catégorie</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                            <input type="file" name="pdf_file" accept="application/pdf"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all">
                            <p class="text-xs text-gray-500 mt-1">Max 50Mo.</p>
                        </div>

                        {{-- Separator if both --}}
                        <div x-show="format === 'pdf_audio'" class="border-t border-gray-200"></div>

                        {{-- Upload Audio --}}
                        <div x-show="format === 'audio' || format === 'pdf_audio'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fichier Audio (MP3)</label>
                            <input type="file" name="audio_file" accept="audio/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition-all">
                            <p class="text-xs text-gray-500 mt-1">Max 100Mo. Format MP3 recommandé.</p>
                        </div>
                    </div>

                </div>

            </div>

            {{-- COLONNE DROITE (Latérale) --}}
            <div class="space-y-6">

                {{-- CARTE 3 : COUVERTURE --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Couverture</h2>

                    <div class="relative group">
                        {{-- Zone de preview --}}
                        <div class="aspect-[2/3] w-full bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:border-indigo-400 transition-colors relative">

                            {{-- Image Preview via Alpine --}}
                            <template x-if="coverPreview">
                                <img :src="coverPreview" class="absolute inset-0 w-full h-full object-cover">
                            </template>

                            {{-- Placeholder --}}
                            <template x-if="!coverPreview">
                                <div class="text-center p-4">
                                    <i data-feather="image" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                    <p class="text-xs text-gray-500">Cliquez pour ajouter une image</p>
                                </div>
                            </template>

                            {{-- Input caché qui couvre tout --}}
                            <input type="file" name="cover" accept="image/*" @change="updatePreview"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">Format recommandé : JPG, PNG (Max 2Mo)</p>
                </div>

                {{-- CARTE 4 : TARIFICATION & ACCÈS --}}
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

                        {{-- Champ Prix Conditionnel --}}
                        <div x-show="accessType === 'paid'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix de vente</label>
                            <div class="relative rounded-md shadow-sm">
                                <input type="number" name="price" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 border pl-3 pr-12 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
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
                                <input type="radio" name="status" value="draft" checked class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Brouillon</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="published" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Publier</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">
                            En "Brouillon", le livre ne sera pas visible sur l'application mobile.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@endsection
