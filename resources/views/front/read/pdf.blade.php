@extends('front.layouts.app')

@section('title', 'Lecture – ' . $book->title)

@section('content')

<style>
    /* Zone de lecture fullscreen */
    .reader-container {
        @apply w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden;
        height: 85vh;
    }

    /* Loader élégant */
    .loader {
        border: 4px solid #e5e7eb;
        border-top: 4px solid #E0551B;
        border-radius: 50%;
        width: 34px;
        height: 34px;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        100% { transform: rotate(360deg); }
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- ==========================================
         HEADER BAR (Retour + Infos + Progression)
    =========================================== --}}
    <div class="flex items-center justify-between mb-6">

        {{-- Retour --}}
        <a href="{{ route('books.show', $book->slug) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="text-sm">Retour au livre</span>
        </a>

        {{-- Titre --}}
        <div class="text-center flex-1 px-4">
            <h1 class="text-xl font-semibold text-slate-900 truncate">
                {{ $book->title }}
            </h1>
            <p class="text-xs text-slate-500">{{ $book->author->name ?? '' }}</p>
        </div>

        {{-- Progression --}}
        <div class="text-right">
            <p class="text-xs text-slate-500 mb-1">Progression</p>

            <div class="w-36 h-2 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full bg-[#079C25] transition-all"
                     style="width: {{ $progress }}%;">
                </div>
            </div>

            <p class="text-xs text-slate-600 mt-1">{{ $progress }}%</p>
        </div>

    </div>



    {{-- ==========================================
         ZONE LECTURE (PDF Viewer ou Flipbook)
    =========================================== --}}
    <div class="reader-container relative flex items-center justify-center">

        {{-- LOADER --}}
        <div id="loader" class="absolute inset-0 flex items-center justify-center bg-white z-20">
            <div class="loader"></div>
        </div>

        {{-- PDF IFRAME --}}
        <iframe id="pdfFrame"
                src="{{ asset('storage/'.$book->pdf_file) }}#toolbar=0&navpanes=0&scrollbar=0"
                class="w-full h-full"
                onload="document.getElementById('loader').style.display='none';">
        </iframe>

    </div>


    {{-- ==========================================
         FOOTER ACTIONS
    =========================================== --}}
    <div class="mt-6 flex items-center justify-between">

        {{-- Bouton précédent --}}
        <button class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm flex items-center gap-2">
            <i data-lucide="chevron-left" class="w-4 h-4"></i> Page précédente
        </button>

        {{-- Marquer progression --}}
        <button id="saveProgress"
                class="px-5 py-2 rounded-lg bg-[#E0551B] text-white text-sm hover:bg-[#c44a19] flex items-center gap-2">
            <i data-lucide="bookmark" class="w-4 h-4"></i>
            Sauvegarder la progression
        </button>

        {{-- Bouton suivant --}}
        <button class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm flex items-center gap-2">
            Page suivante <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>

    </div>

</div>



{{-- ==========================================
     SCRIPT DE SAUVEGARDE DE PROGRESSION
========================================== --}}

<script>
document.getElementById('saveProgress').addEventListener('click', function () {

    fetch("{{ route('progress.update', $book->id) }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ progress: 30 }) // valeur temporaire
    })
    .then(res => res.json())
    .then(data => {
        alert("Progression sauvegardée !");
    });

});
</script>

@endsection
