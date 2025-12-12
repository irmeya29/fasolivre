@extends('admin.layouts.app')

@section('title', 'Nouvel Auteur')

@section('content')

{{-- Init Alpine pour la preview de la photo --}}
<div class="max-w-6xl mx-auto" x-data="{
    photoPreview: null,
    updatePreview(event) {
        const file = event.target.files[0];
        if (file) {
            this.photoPreview = URL.createObjectURL(file);
        }
    }
}">

    {{-- HEADER AVEC BOUTONS D'ACTION --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.authors.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Ajouter un auteur</h1>
                <p class="text-sm text-gray-500">Créez le profil d'un nouvel écrivain.</p>
            </div>
        </div>
        <button type="submit" form="createAuthorForm" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
            <i data-feather="save" class="w-4 h-4"></i>
            Enregistrer l'auteur
        </button>
    </div>

    <form id="createAuthorForm" action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- COLONNE GAUCHE (Infos principales) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARTE 1 : IDENTITÉ --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-feather="user" class="w-4 h-4 text-indigo-500"></i>
                        Identité & Biographie
                    </h2>

                    <div class="space-y-5">
                        {{-- Nom --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors placeholder-gray-400"
                                placeholder="Ex: Victor Hugo">
                            @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Bio --}}
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Biographie</label>
                            <textarea name="bio" id="bio" rows="5"
                                class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-colors placeholder-gray-400"
                                placeholder="Quelques mots sur le parcours de l'auteur..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- CARTE 2 : RÉSEAUX SOCIAUX --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-feather="share-2" class="w-4 h-4 text-indigo-500"></i>
                        Réseaux & Contact
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Website --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Web Personnel</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i data-feather="globe" class="w-4 h-4"></i>
                                </div>
                                <input type="url" name="website" class="pl-10 w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="https://...">
                            </div>
                        </div>

                        {{-- Facebook --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i data-feather="facebook" class="w-4 h-4"></i>
                                </div>
                                <input type="url" name="facebook" class="pl-10 w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="Lien profil...">
                            </div>
                        </div>

                        {{-- Instagram --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i data-feather="instagram" class="w-4 h-4"></i>
                                </div>
                                <input type="url" name="instagram" class="pl-10 w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="Lien profil...">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- COLONNE DROITE (Latérale) --}}
            <div class="space-y-6">

                {{-- CARTE 3 : PHOTO DE PROFIL --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Photo de profil</h2>

                    <div class="flex flex-col items-center">
                        <div class="relative group cursor-pointer w-40 h-40">
                            {{-- Container Rond --}}
                            <div class="w-40 h-40 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:border-indigo-400 transition-colors relative shadow-sm">

                                {{-- Preview Alpine --}}
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="absolute inset-0 w-full h-full object-cover">
                                </template>

                                {{-- Placeholder --}}
                                <template x-if="!photoPreview">
                                    <div class="text-center p-2">
                                        <i data-feather="camera" class="w-8 h-8 text-gray-400 mx-auto mb-1"></i>
                                        <span class="text-xs text-gray-500">Ajouter</span>
                                    </div>
                                </template>

                                {{-- Input caché sur toute la zone --}}
                                <input type="file" name="photo" accept="image/*" @change="updatePreview"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            </div>

                            {{-- Petit bouton edit flottant --}}
                            <div class="absolute bottom-1 right-1 bg-white p-1.5 rounded-full shadow border border-gray-200 text-gray-500 group-hover:text-indigo-600 transition-colors pointer-events-none">
                                <i data-feather="edit-2" class="w-3 h-3"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-4 text-center">Format carré recommandé.<br>Max 2Mo.</p>
                    </div>
                </div>

                {{-- CARTE 4 : STATUT --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Visibilité</h2>

                    <div class="flex items-center justify-between">
                        <span class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900">Statut Auteur</span>
                            <span class="text-xs text-gray-500">Visible sur la plateforme</span>
                        </span>

                        {{-- Toggle Switch Style --}}
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    feather.replace();
</script>

@endsection
