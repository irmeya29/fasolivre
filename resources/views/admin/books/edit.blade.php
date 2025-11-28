@extends('admin.layouts.app')

@section('title', 'Modifier Livre')

@section('content')

<div class="max-w-5xl mx-auto">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        Modifier : {{ $book->title }}
    </h1>

    <form action="{{ route('admin.books.update', $book) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-8">
        @csrf
        @method('PUT')



        {{-- SECTION : Informations générales --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">
            <h2 class="text-xl font-semibold">Informations générales</h2>

            {{-- Title --}}
            <div>
                <label class="text-gray-700 font-medium">Titre</label>
                <input type="text" name="title"
                       value="{{ $book->title }}"
                       class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>

            {{-- Description --}}
            <div>
                <label class="text-gray-700 font-medium">Description</label>
                <textarea name="description" rows="4"
                          class="mt-1 w-full p-3 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                >{{ $book->description }}</textarea>
            </div>
        </div>





        {{-- SECTION : Classification --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">

            <h2 class="text-xl font-semibold">Classification</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Auteur --}}
                <div>
                    <label class="text-gray-700 font-medium">Auteur</label>
                    <select name="author_id"
                        class="mt-1 w-full p-3 border rounded-lg"
                        required>
                        @foreach($authors as $a)
                            <option value="{{ $a->id }}"
                                {{ $book->author_id == $a->id ? 'selected' : '' }}>
                                {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Catégorie --}}
                <div>
                    <label class="text-gray-700 font-medium">Catégorie</label>
                    <select name="category_id"
                        class="mt-1 w-full p-3 border rounded-lg"
                        required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}"
                                {{ $book->category_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Format --}}
                <div>
                    <label class="text-gray-700 font-medium">Format</label>
                    <select name="format" id="formatSelect"
                        class="mt-1 w-full p-3 border rounded-lg"
                        required>
                        <option value="pdf" {{ $book->format == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="audio" {{ $book->format == 'audio' ? 'selected' : '' }}>Audio</option>
                        <option value="pdf_audio" {{ $book->format == 'pdf_audio' ? 'selected' : '' }}>PDF + Audio</option>
                    </select>
                </div>

            </div>

        </div>




        {{-- SECTION : Accès --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">

            <h2 class="text-xl font-semibold">Accès</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Access type --}}
                <div>
                    <label class="text-gray-700 font-medium">Type d'accès</label>
                    <select name="access_type" id="accessType"
                        class="mt-1 w-full p-3 border rounded-lg">

                        <option value="free" {{ $book->access_type === 'free' ? 'selected' : '' }}>Gratuit</option>

                        <option value="paid" {{ $book->access_type === 'paid' ? 'selected' : '' }}>Payant</option>

                        <option value="subscription" {{ $book->access_type === 'subscription' ? 'selected' : '' }}>Abonnement</option>

                    </select>
                </div>

                {{-- Price only if paid --}}
                <div id="priceWrapper" class="{{ $book->access_type !== 'paid' ? 'hidden' : '' }}">
                    <label class="text-gray-700 font-medium">Prix (FCFA)</label>
                    <input type="number" name="price"
                           value="{{ $book->price }}"
                           class="mt-1 w-full p-3 border rounded-lg"
                           min="0">
                </div>

            </div>

        </div>




        {{-- SECTION : Upload --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-8">

            <h2 class="text-xl font-semibold">Fichiers</h2>

            {{-- COVER upload (drag-drop + preview) --}}
            <div>
                <label class="text-gray-700 font-medium">Cover</label>

                <div id="dropCover"
                    class="mt-2 border-2 border-dashed rounded-xl p-6 text-center cursor-pointer
                           hover:bg-indigo-50 transition">

                    <input type="file" name="cover" id="coverInput" class="hidden">

                    <div id="coverPreview" class="flex flex-col items-center gap-2">

                        @if($book->cover)
                            <img src="{{ asset('storage/'.$book->cover) }}"
                                 class="w-32 h-40 object-cover rounded-xl shadow">
                        @else
                            <i data-feather="image" class="w-10 h-10 text-gray-400"></i>
                            <p class="text-gray-500">Déposer ou cliquer pour téléverser</p>
                        @endif

                    </div>

                </div>
            </div>



            {{-- PDF --}}
            <div id="pdfWrapper" class="{{ $book->format == 'audio' ? 'hidden' : '' }}">
                <label class="text-gray-700 font-medium">Fichier PDF</label>

                @if($book->pdf_file)
                    <p class="text-gray-500 text-sm mb-1">
                        Fichier actuel : <strong>{{ basename($book->pdf_file) }}</strong>
                    </p>
                @endif

                <input type="file" name="pdf_file"
                       class="mt-2 w-full p-3 border rounded-lg">
            </div>



            {{-- AUDIO --}}
            <div id="audioWrapper"
                 class="{{ $book->format == 'pdf' ? 'hidden' : '' }}">
                <label class="text-gray-700 font-medium">Fichier Audio (MP3)</label>

                @if($book->audio_file)
                    <p class="text-gray-500 text-sm mb-1">
                        Fichier actuel : <strong>{{ basename($book->audio_file) }}</strong>
                    </p>
                @endif

                <input type="file" name="audio_file"
                       class="mt-2 w-full p-3 border rounded-lg">
            </div>

        </div>




        {{-- Status --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">

            <h2 class="text-xl font-semibold">Publication</h2>

            <select name="status"
                class="w-full p-3 border rounded-lg">
                <option value="draft" {{ $book->status == 'draft' ? 'selected' : '' }}>Brouillon</option>
                <option value="published" {{ $book->status == 'published' ? 'selected' : '' }}>Publié</option>
            </select>

        </div>




        {{-- Submit --}}
        <div class="flex gap-4">
            <button class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow">
                Mettre à jour
            </button>

            <a href="{{ route('admin.books.index') }}"
                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>

</div>



{{-- SCRIPT : preview + form dynamique --}}
<script>

    // === PRICE DISPLAY ===
    const priceWrapper = document.getElementById('priceWrapper');
    document.getElementById('accessType').addEventListener('change', function() {
        priceWrapper.classList.toggle('hidden', this.value !== 'paid');
    });


    // === FORMAT DISPLAY ===
    const pdfWrapper = document.getElementById('pdfWrapper');
    const audioWrapper = document.getElementById('audioWrapper');

    document.getElementById('formatSelect').addEventListener('change', function() {
        let f = this.value;
        pdfWrapper.classList.toggle('hidden', f === 'audio');
        audioWrapper.classList.toggle('hidden', f === 'pdf');
    });


    // === COVER PREVIEW ===
    const drop = document.getElementById('dropCover');
    const coverInput = document.getElementById('coverInput');
    const preview = document.getElementById('coverPreview');

    drop.addEventListener('click', () => coverInput.click());

    coverInput.addEventListener('change', e => {
        let file = e.target.files[0];
        previewFile(file);
    });

    function previewFile(file) {
        const reader = new FileReader();
        reader.onload = () => {
            preview.innerHTML = `
                <img src="${reader.result}" class="w-32 h-40 object-cover rounded-xl shadow">
            `;
        };
        reader.readAsDataURL(file);
    }

</script>

<script>feather.replace();</script>

@endsection
