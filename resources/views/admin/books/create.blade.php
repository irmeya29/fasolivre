@extends('admin.layouts.app')

@section('title', 'Nouveau Livre')

@section('content')

<div class="max-w-5xl mx-auto">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Créer un livre</h1>

    <form action="{{ route('admin.books.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-8"
    >
        @csrf

        {{-- SECTION : Informations générales --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">

            <h2 class="text-xl font-semibold">Informations générales</h2>

            {{-- Title --}}
            <div>
                <label class="text-gray-700 font-medium">Titre du livre</label>
                <input type="text" name="title"
                    class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Ex : L'art de penser"
                    required>
            </div>

            {{-- Description --}}
            <div>
                <label class="text-gray-700 font-medium">Description</label>
                <textarea name="description" rows="4"
                    class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Résumé du livre..."></textarea>
            </div>

        </div>




        {{-- SECTION : Auteur / Catégorie / Format --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">

            <h2 class="text-xl font-semibold">Classification</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Auteur --}}
                <div>
                    <label class="text-gray-700 font-medium">Auteur</label>
                    <select name="author_id"
                        class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        <option value="">Sélectionner...</option>
                        @foreach($authors as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Catégorie --}}
                <div>
                    <label class="text-gray-700 font-medium">Catégorie</label>
                    <select name="category_id"
                        class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        <option value="">Sélectionner...</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Format --}}
                <div>
                    <label class="text-gray-700 font-medium">Format</label>
                    <select name="format" id="formatSelect"
                        class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        <option value="pdf">PDF</option>
                        <option value="audio">Audio</option>
                        <option value="pdf_audio">PDF + Audio</option>
                    </select>
                </div>

            </div>
        </div>




        {{-- SECTION : Access / prix --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">

            <h2 class="text-xl font-semibold">Accès</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Access type --}}
                <div>
                    <label class="text-gray-700 font-medium">Type d'accès</label>
                    <select name="access_type" id="accessType"
                        class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="free">Gratuit</option>
                        <option value="paid">Payant</option>
                        <option value="subscription">Abonnement</option>
                    </select>
                </div>

                {{-- Price --}}
                <div id="priceWrapper" class="hidden">
                    <label class="text-gray-700 font-medium">Prix (FCFA)</label>
                    <input type="number" name="price" min="0"
                        class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Ex : 2000">
                </div>

            </div>
        </div>




        {{-- SECTION : Upload Files --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">

            <h2 class="text-xl font-semibold">Fichiers</h2>

            {{-- COVER upload (drag & drop preview) --}}
            <div>
                <label class="text-gray-700 font-medium">Cover</label>

                <div id="dropCover"
                     class="mt-2 border-2 border-dashed rounded-xl p-6 text-center cursor-pointer
                            hover:bg-indigo-50 transition">

                    <input type="file" name="cover" id="coverInput" class="hidden">

                    <div id="coverPreview" class="flex flex-col items-center gap-2">
                        <i data-feather="image" class="w-10 h-10 text-gray-400"></i>
                        <p class="text-gray-500">Déposer ou cliquer pour téléverser</p>
                    </div>

                </div>
            </div>


            {{-- PDF upload --}}
            <div id="pdfWrapper">
                <label class="text-gray-700 font-medium">Fichier PDF</label>
                <input type="file" name="pdf_file"
                       class="mt-2 w-full p-3 border rounded-lg">
            </div>

            {{-- Audio upload --}}
            <div id="audioWrapper" class="hidden">
                <label class="text-gray-700 font-medium">Fichier Audio (MP3)</label>
                <input type="file" name="audio_file"
                       class="mt-2 w-full p-3 border rounded-lg">
            </div>

        </div>




        {{-- Status --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">

            <h2 class="text-xl font-semibold">Publication</h2>

            <select name="status"
                class="w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                <option value="draft">Brouillon</option>
                <option value="published">Publié</option>
            </select>

        </div>




        {{-- Submit buttons --}}
        <div class="flex gap-4">
            <button class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow">
                Enregistrer
            </button>

            <a href="{{ route('admin.books.index') }}"
                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>




{{-- SCRIPT : Preview + drag drop + affichage dynamique --}}
<script>

    // --- Affichage du prix ---
    const priceWrapper = document.getElementById('priceWrapper');
    document.getElementById('accessType').addEventListener('change', function() {
        priceWrapper.classList.toggle('hidden', this.value !== 'paid');
    });

    // --- Format PDF / Audio ---
    const pdfWrapper = document.getElementById('pdfWrapper');
    const audioWrapper = document.getElementById('audioWrapper');

    document.getElementById('formatSelect').addEventListener('change', function() {

        let format = this.value;

        pdfWrapper.classList.toggle('hidden', format === 'audio');
        audioWrapper.classList.toggle('hidden', format === 'pdf');
    });

    // --- Drag & Drop Cover ---
    const drop = document.getElementById('dropCover');
    const coverInput = document.getElementById('coverInput');
    const preview = document.getElementById('coverPreview');

    drop.addEventListener('click', () => coverInput.click());

    coverInput.addEventListener('change', e => {
        let file = e.target.files[0];
        showPreview(file);
    });

    function showPreview(file) {
        const reader = new FileReader();

        reader.onload = () => {
            preview.innerHTML = `<img src="${reader.result}" class="w-32 h-40 object-cover rounded-xl shadow">`;
        };

        reader.readAsDataURL(file);
    }

</script>

<script>feather.replace();</script>

@endsection
