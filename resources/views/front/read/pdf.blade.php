@extends('front.layouts.app')

@section('title', 'Lecture – '.$book->title)

@section('content')

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- HEADER LECTURE --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2">
            <i data-lucide="book-open" class="w-5 h-5 text-[#E0551B]"></i>
            {{ $book->title }}
        </h1>

        <a href="{{ route('books.show', $book->slug) }}"
           class="text-sm text-slate-500 hover:text-[#E0551B] flex items-center gap-1">
            <i data-lucide="arrow-left"></i> Retour
        </a>
    </div>


    {{-- PDF VIEWER WRAPPER --}}
    <div class="bg-white rounded-2xl shadow p-4">

        {{-- TOOLBAR --}}
        <div class="flex items-center justify-between border-b pb-3 mb-3">

            {{-- Left --}}
            <div class="flex items-center gap-3">

                <button id="prevPage" class="pdf-btn">
                    <i data-lucide="chevron-left"></i>
                </button>

                <span id="pageInfo" class="text-sm text-slate-600">
                    Page <span id="pageNum">1</span> / <span id="pageCount">0</span>
                </span>

                <button id="nextPage" class="pdf-btn">
                    <i data-lucide="chevron-right"></i>
                </button>

            </div>

            {{-- Zoom --}}
            <div class="flex items-center gap-2">
                <button id="zoomOut" class="pdf-btn">
                    <i data-lucide="minus"></i>
                </button>

                <button id="zoomIn" class="pdf-btn">
                    <i data-lucide="plus"></i>
                </button>
            </div>
        </div>


        {{-- CANVAS PDF --}}
        <div class="w-full overflow-auto flex justify-center">
            <canvas id="pdfCanvas" class="rounded-lg shadow"></canvas>
        </div>

    </div>


    {{-- PROGRESSION --}}
    <div class="mt-6">
        <p class="text-sm text-slate-600 mb-1">
            Progression de lecture : <span id="progressValue">{{ $progress }}</span>%
        </p>

        <div class="w-full bg-slate-200 rounded-full h-2">
            <div id="progressBar"
                 class="h-2 rounded-full bg-gradient-to-r from-[#E0551B] to-[#079C25]"
                 style="width: {{ $progress }}%">
            </div>
        </div>
    </div>

</div>


{{-- ===============================
     PDF.JS + CUSTOM SCRIPT
================================ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
<script>
    const url = "{{ asset('storage/' . $book->pdf_file) }}";

    let pdfDoc = null,
        pageNum = 1,
        scale = 1.2,
        canvas = document.getElementById('pdfCanvas'),
        ctx = canvas.getContext('2d');

    const progressBar = document.getElementById('progressBar');
    const progressValue = document.getElementById('progressValue');


    /* ============================
       LOAD DOCUMENT
    ============================ */
    pdfjsLib.getDocument(url).promise.then(pdf => {
        pdfDoc = pdf;
        document.getElementById('pageCount').textContent = pdf.numPages;
        renderPage(pageNum);

        // Calcul progression initiale
        updateProgress();
    });


    /* ============================
       RENDER PAGE
    ============================ */
    function renderPage(num) {

        pdfDoc.getPage(num).then(page => {

            let viewport = page.getViewport({ scale: scale });

            canvas.height = viewport.height;
            canvas.width  = viewport.width;

            let renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            page.render(renderContext);

            document.getElementById('pageNum').textContent = num;

            // Update progression après changement de page
            updateProgressDebounced();
        });
    }


    /* ============================
       PAGE CONTROLS
    ============================ */
    document.getElementById('prevPage').addEventListener('click', () => {
        if (pageNum <= 1) return;
        pageNum--;
        renderPage(pageNum);
    });

    document.getElementById('nextPage').addEventListener('click', () => {
        if (pageNum >= pdfDoc.numPages) return;
        pageNum++;
        renderPage(pageNum);
    });


    /* ============================
       ZOOM CONTROLS
    ============================ */
    document.getElementById('zoomIn').addEventListener('click', () => {
        scale += 0.2;
        renderPage(pageNum);
    });

    document.getElementById('zoomOut').addEventListener('click', () => {
        if (scale <= 0.6) return;
        scale -= 0.2;
        renderPage(pageNum);
    });


    /* ================================
       PROGRESSION DE LECTURE
    ================================ */
    function updateProgress() {
        let percent = Math.floor((pageNum / pdfDoc.numPages) * 100);

        progressValue.textContent = percent;
        progressBar.style.width = percent + "%";

        // Enregistrer progression (AJAX)
        saveProgress(percent);
    }

    // Anti-spam (évite d'envoyer 50 requêtes par seconde)
    let timeout = null;
    function updateProgressDebounced() {
        clearTimeout(timeout);
        timeout = setTimeout(updateProgress, 800);
    }


    /* ================================
       SAVE PROGRESS TO BACKEND
    ================================ */
    function saveProgress(percent) {

        fetch("{{ route('read.saveProgress', $book->id) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ progress: percent })
        })
        .then(res => res.json())
        .then(data => {
            // console.log("Progress saved:", data);
        });
    }

</script>


{{-- ============================
     STYLES
============================= --}}
<style>
    .pdf-btn {
        @apply flex items-center justify-center px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition;
    }
</style>

@endsection
