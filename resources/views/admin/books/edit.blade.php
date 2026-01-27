@extends('admin.layouts.app')

@section('title', 'Modifier le livre')

@section('content')

<div class="max-w-6xl mx-auto"
     x-data="{
        format: @js(old('format', $book->format)),
        accessType: @js(old('access_type', $book->access_type)),
        coverPreview: @js($book->cover ? asset('storage/'.$book->cover) : null),
        hasPdf: @js(!empty($book->pdf_file)),
        hasAudio: @js(!empty($book->audio_file)),
        updatePreview(event) {
            const file = event.target.files[0];
            if (file) this.coverPreview = URL.createObjectURL(file);
        }
     }">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.books.index') }}"
               class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Modifier : {{ $book->title }}</h1>
                <p class="text-sm text-gray-500">Mettez à jour les informations et les fichiers.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" form="editBookForm"
                    class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                <i data-feather="save" class="w-4 h-4"></i>
                Enregistrer les modifications
            </button>
        </div>
    </div>

    <form id="editBookForm" action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- COLONNE GAUCHE --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARTE 1 : INFOS --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-feather="info" class="w-4 h-4 text-indigo-500"></i>
                        Informations Générales
                    </h2>

                    <div class="space-y-5">
                        {{-- Titre --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Titre du livre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required
                                   value="{{ old('title', $book->title) }}"
                                   class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors">
                            @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Synopsis / Description
                            </label>
                            <textarea name="description" id="description" rows="5"
                                      class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors">{{ old('description', $book->description) }}</textarea>
                            @error('description') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- CARTE 2 : CONTENU --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-feather="file-text" class="w-4 h-4 text-indigo-500"></i>
                        Contenu & Fichiers
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Auteur --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Auteur <span class="text-red-500">*</span>
                            </label>
                            <select name="author_id" required
                                    class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-white">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" @selected(old('author_id', $book->author_id) == $author->id)>
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('author_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Catégorie --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <select name="category_id"
                                    class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-white">
                                <option value="">— Aucune —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- FORMAT --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format du livre</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="format" value="pdf" x-model="format" class="peer sr-only">
                                <div class="rounded-lg border border-gray-200 p-3 text-center hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                    <span class="block text-sm font-medium">PDF</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="format" value="audio" x-model="format" class="peer sr-only">
                                <div class="rounded-lg border border-gray-200 p-3 text-center hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                    <span class="block text-sm font-medium">Audio</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="format" value="pdf_audio" x-model="format" class="peer sr-only">
                                <div class="rounded-lg border border-gray-200 p-3 text-center hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                    <span class="block text-sm font-medium">PDF + Audio</span>
                                </div>
                            </label>
                        </div>
                        @error('format') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- UPLOADS --}}
                    <div class="space-y-4 bg-gray-50 rounded-lg p-4 border border-dashed border-gray-300">

                        {{-- PDF --}}
                        <div x-show="format === 'pdf' || format === 'pdf_audio'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Fichier PDF
                                <span class="text-red-500" x-show="(format === 'pdf' || format === 'pdf_audio') && !hasPdf">*</span>
                            </label>

                            @if($book->pdf_file)
                                <div class="flex items-center gap-2 mb-2 px-3 py-2 bg-white border border-green-200 rounded-md text-sm text-green-700">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                    <span class="truncate">Actuel : {{ basename($book->pdf_file) }}</span>
                                </div>
                            @endif

                            <input type="file" name="pdf_file" accept="application/pdf"
                                   :required="(format === 'pdf' || format === 'pdf_audio') && !hasPdf"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all">
                            <p class="text-xs text-gray-500 mt-1">Max 50Mo.</p>
                            @error('pdf_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div x-show="format === 'pdf_audio'" class="border-t border-gray-200"></div>

                        {{-- AUDIO --}}
                        <div x-show="format === 'audio' || format === 'pdf_audio'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Fichier Audio
                                <span class="text-red-500" x-show="(format === 'audio' || format === 'pdf_audio') && !hasAudio">*</span>
                            </label>

                            @if($book->audio_file)
                                <div class="flex items-center gap-2 mb-2 px-3 py-2 bg-white border border-purple-200 rounded-md text-sm text-purple-700">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                    <span class="truncate">Actuel : {{ basename($book->audio_file) }}</span>
                                </div>
                            @endif

                            <input type="file" name="audio_file" accept="audio/*"
                                   :required="(format === 'audio' || format === 'pdf_audio') && !hasAudio"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition-all">
                            <p class="text-xs text-gray-500 mt-1">Max 100Mo. MP3 recommandé.</p>
                            @error('audio_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </div>
            </div>

            {{-- COLONNE DROITE --}}
            <div class="space-y-6">

                {{-- COUVERTURE --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Couverture</h2>

                    <div class="relative group">
                        <div class="aspect-[2/3] w-full bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:border-indigo-400 transition-colors relative">

                            <template x-if="coverPreview">
                                <img :src="coverPreview" class="absolute inset-0 w-full h-full object-cover">
                            </template>

                            <template x-if="!coverPreview">
                                <div class="text-center p-4">
                                    <i data-feather="image" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                    <p class="text-xs text-gray-500">Ajouter une couverture</p>
                                </div>
                            </template>

                            <input type="file" name="cover" accept="image/*" @change="updatePreview"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>

                    @error('cover') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-2 text-center">JPG/PNG/WebP (Max 2Mo)</p>
                </div>

                {{-- ACCÈS & PRIX --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Accès & Prix</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type d'accès</label>
                            <select name="access_type" x-model="accessType"
                                    class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <option value="free">Gratuit</option>
                                <option value="paid">Payant (Achat unique)</option>
                                <option value="subscription">Abonnement (Premium)</option>
                            </select>
                            @error('access_type') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div x-show="accessType === 'paid'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Prix de vente <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <input type="number" name="price" min="0"
                                       value="{{ old('price', $book->price) }}"
                                       :required="accessType === 'paid'"
                                       :disabled="accessType !== 'paid'"
                                       class="w-full rounded-lg border-gray-300 border pl-3 pr-12 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 sm:text-sm">FCFA</span>
                                </div>
                            </div>
                            @error('price') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- PUBLICATION --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Publication</h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">État du livre</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="draft"
                                       @checked(old('status', $book->status) === 'draft')
                                       class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Brouillon</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="published"
                                       @checked(old('status', $book->status) === 'published')
                                       class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">Publié</span>
                            </label>
                        </div>
                        @error('status') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@endsection
