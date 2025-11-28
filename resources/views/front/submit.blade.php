@extends('front.layouts.app')

@section('title', 'Soumettre un manuscrit – Fasolivre')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-12">

    {{-- Header --}}
    <div class="mb-10 text-center space-y-3">
        <h1 class="text-3xl font-semibold text-slate-900 flex items-center justify-center gap-2">
            <i data-lucide="file-plus" class="w-7 h-7 text-indigo-600"></i>
            Soumettre un manuscrit
        </h1>
        <p class="text-slate-600 text-sm">
            Envoie ton œuvre et notre équipe éditoriale la traitera dans les plus brefs délais.
        </p>
    </div>

    {{-- Success --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif


    {{-- FORM --}}
    <form method="POST" action="{{ route('submit.store') }}" enctype="multipart/form-data"
          class="bg-white shadow-xl rounded-2xl p-8 space-y-6">

        @csrf

        {{-- Title --}}
        <div>
            <label class="text-sm font-medium text-slate-700">Titre du manuscrit</label>
            <input type="text" name="title"
                   value="{{ old('title') }}"
                   class="mt-1 w-full px-4 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500"
                   required>
            @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="text-sm font-medium text-slate-700">Résumé / Description</label>
            <textarea name="description" rows="4"
                      class="mt-1 w-full px-4 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500"
                      required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- PDF UPLOAD (DROPZONE STYLE) --}}
        <div>
            <label class="text-sm font-medium text-slate-700">Fichier PDF</label>

            <div onclick="document.getElementById('pdfInput').click()"
                 class="mt-2 border-2 border-dashed border-slate-300 rounded-2xl p-6 cursor-pointer hover:border-indigo-400 transition">

                <div class="flex flex-col items-center text-center gap-2">
                    <i data-lucide="upload-cloud" class="w-10 h-10 text-slate-400"></i>
                    <p class="text-sm text-slate-500">Déposer votre manuscrit PDF ou cliquer ici</p>
                </div>

                <input type="file" id="pdfInput" name="pdf" accept="application/pdf" class="hidden" required>
            </div>

            @error('pdf')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-indigo-600 text-white px-5 py-3 rounded-xl font-medium hover:bg-indigo-700">
            <i data-lucide="send" class="w-5 h-5"></i>
            Envoyer le manuscrit
        </button>

    </form>

</div>

@endsection
