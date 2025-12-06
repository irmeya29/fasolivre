@extends('front.layouts.app')

@section('title', 'Soumettre un manuscrit – Fasolivre')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
    }

    .glass {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .dropzone:hover {
        border-color: var(--faso-orange);
        background: #fff7f3;
    }
</style>

<div class="max-w-3xl mx-auto px-4 py-14">

    {{-- HEADER --}}
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-slate-900 flex items-center justify-center gap-3">
            <i data-lucide="file-plus" class="w-8 h-8 text-[var(--faso-orange)]"></i>
            Soumettre un manuscrit
        </h1>
        <p class="text-slate-600 text-sm mt-3">
            Partage ton œuvre avec notre équipe éditoriale. Nous te contacterons après évaluation.
        </p>
    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-8 p-4 bg-green-100 text-green-700 rounded-xl border border-green-200">
            <i data-lucide="check-circle" class="w-4 h-4 inline-block mr-1"></i>
            {{ session('success') }}
        </div>
    @endif


    {{-- FORMULAIRE --}}
    <form method="POST" action="{{ route('submit.store') }}" enctype="multipart/form-data"
          class="glass shadow-xl rounded-3xl p-10 space-y-8 border border-white/40">

        @csrf

        {{-- TITRE --}}
        <div class="space-y-1">
            <label class="text-sm font-semibold text-slate-700">Titre du manuscrit</label>
            <input type="text"
                   name="title"
                   class="w-full mt-1 px-4 py-3 rounded-xl border border-slate-300 text-sm shadow-sm focus:ring-2 focus:ring-[var(--faso-orange)] focus:border-transparent"
                   value="{{ old('title') }}"
                   required>
            @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- DESCRIPTION --}}
        <div class="space-y-1">
            <label class="text-sm font-semibold text-slate-700">Résumé / Description</label>
            <textarea name="description"
                      rows="4"
                      class="w-full mt-1 px-4 py-3 rounded-xl border border-slate-300 text-sm shadow-sm focus:ring-2 focus:ring-[var(--faso-orange)] focus:border-transparent"
                      required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- FILE UPLOAD (DROPZONE PREMIUM) --}}
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Manuscrit (PDF uniquement)</label>

            <div onclick="document.getElementById('pdfInput').click()"
                 class="dropzone border-2 border-dashed border-slate-300 rounded-2xl p-8 cursor-pointer transition duration-300">

                <div class="flex flex-col items-center gap-3 text-center">
                    <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center">
                        <i data-lucide="upload-cloud" class="w-7 h-7 text-slate-500"></i>
                    </div>

                    <div>
                        <p class="text-sm text-slate-700 font-semibold">Déposer votre fichier PDF</p>
                        <p class="text-xs text-slate-500">Ou cliquer ici pour sélectionner un fichier</p>
                    </div>
                </div>

                <input type="file"
                       id="pdfInput"
                       name="pdf"
                       accept="application/pdf"
                       class="hidden"
                       required>
            </div>

            @error('pdf')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>


        {{-- CTA SUBMIT --}}
        <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-xl font-semibold text-white
                       bg-gradient-to-r from-[var(--faso-orange)] to-[var(--faso-green)]
                       shadow-lg hover:opacity-90 transition">
            <i data-lucide="send" class="w-5 h-5"></i>
            Envoyer mon manuscrit
        </button>

    </form>

</div>

@endsection
