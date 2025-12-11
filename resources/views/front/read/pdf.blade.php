@extends('front.layouts.app')

@section('title', 'Lecture – ' . $book->title)

@section('content')

<style>
    /* =========================================
       SÉCURITÉ & ANTI-COPIE
       ========================================= */

    /* Empêche la sélection de texte partout */
    body {
        -webkit-user-select: none; /* Safari */
        -ms-user-select: none; /* IE 10 and IE 11 */
        user-select: none; /* Standard syntax */
        background-color: #f3f4f6;
    }

    /* Cache tout si on essaie d'imprimer */
    @media print {
        body { display: none !important; }
        html::after { content: "Impression interdite sur Fasolivre."; display: block; margin: 2rem; font-size: 20px; }
    }

    /* Calque transparent pour bloquer le drag & drop des images */
    .protection-layer {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: 10;
        background: transparent;
    }

    /* =========================================
       DESIGN DU LECTEUR
       ========================================= */
    .pdf-page-container {
        position: relative; /* Pour le watermark */
        margin-bottom: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    canvas {
        display: block;
        background-color: white;
    }

    /* Filigrane discret */
    .watermark {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 24px;
        color: rgba(0, 0, 0, 0.05); /* Très léger */
        pointer-events: none;
        white-space: nowrap;
        font-weight: bold;
        z-index: 20;
    }

    .sticky-reader-header {
        position: sticky; top: 0; z-index: 50;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #e5e7eb;
    }

    .spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        width: 36px; height: 36px;
        border-radius: 50%;
        border-left-color: #E0551B;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

{{-- On désactive le clic droit sur tout le wrapper principal --}}
<div class="min-h-screen flex flex-col" oncontextmenu="return false;">

    {{-- TOP BAR --}}
    <div class="sticky-reader-header py-3 px-4 shadow-sm">
        <div class="max-w-5xl mx-auto flex items-center justify-between">

            <a href="{{ route('books.show', $book->slug) }}"
               class="flex items-center gap-2 text-slate-600 hover:text-slate-900 transition">
                <div class="p-1.5 rounded-full bg-slate-100 hover:bg-slate-200">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </div>
                <span class="hidden sm:inline text-sm font-medium">Quitter</span>
            </a>

            <div class="flex-1 max-w-md mx-4 flex flex-col items-center">
                <h1 class="text-sm font-semibold text-slate-800 truncate w-full text-center mb-1">{{ $book->title }}</h1>
                <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                    <div id="progressBar" class="h-full bg-[#E0551B] transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <button id="saveProgressBtn" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-900 text-white hover:bg-slate-800 text-xs sm:text-sm font-medium transition">
                <i data-lucide="bookmark" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Sauver</span>
            </button>
        </div>
    </div>

    {{-- ZONE LECTURE --}}
    <div class="flex-1 py-8 px-2 sm:px-4 select-none">

        <div id="loader" class="text-center py-10">
            <div class="spinner"></div>
            <p class="text-slate-500 text-sm mt-2">Sécurisation du document...</p>
        </div>

        {{-- Conteneur PDF --}}
        <div id="pdfViewer" class="w-full max-w-2xl mx-auto hidden"></div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>

<script>
    // URL directe (on va la transformer en blob pour la cacher)
    const rawUrl = "{{ asset('storage/' . $book->pdf_file) }}";
    const initialProgress = {{ $progress }};
    const viewer = document.getElementById('pdfViewer');
    const loader = document.getElementById('loader');

    // Protection basique JS
    document.addEventListener('keydown', function(e) {
        // Bloque Ctrl+P (Print), Ctrl+S (Save), Ctrl+U (Source)
        if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 's' || e.key === 'u')) {
            e.preventDefault();
            alert('Action non autorisée sur cette plateforme.');
        }
    });

    // Chargement via Fetch pour créer un Blob (Cache l'URL réelle dans le network tab après chargement)
    fetch(rawUrl)
        .then(response => response.blob())
        .then(blob => {
            const blobUrl = URL.createObjectURL(blob);
            loadPdf(blobUrl);
        })
        .catch(err => {
            loader.innerHTML = `<p class="text-red-500">Erreur de chargement sécurisé.</p>`;
        });

    let pdfDoc = null;

    function loadPdf(url) {
        pdfjsLib.getDocument(url).promise.then(pdf => {
            pdfDoc = pdf;
            loader.style.display = 'none';
            viewer.classList.remove('hidden');
            renderAllPages();
        });
    }

    async function renderAllPages() {
        for (let num = 1; num <= pdfDoc.numPages; num++) {
            await renderPage(num);
        }
        if (initialProgress > 0) {
            setTimeout(() => {
                const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
                window.scrollTo({ top: (initialProgress / 100) * maxScroll, behavior: 'smooth' });
            }, 500);
        }
    }

    async function renderPage(num) {
        const page = await pdfDoc.getPage(num);

        // Calcul Responsive
        const containerWidth = viewer.clientWidth;
        const unscaledViewport = page.getViewport({ scale: 1 });
        let scale = containerWidth / unscaledViewport.width;
        if (scale > 1.5) scale = 1.5;

        const viewport = page.getViewport({ scale: scale });

        // Création du wrapper pour chaque page (Canvas + Watermark + Protection)
        const wrapper = document.createElement('div');
        wrapper.className = 'pdf-page-container mx-auto max-w-max';

        const canvas = document.createElement('canvas');
        canvas.className = 'rounded shadow-sm';
        const ctx = canvas.getContext('2d');

        canvas.height = viewport.height;
        canvas.width = viewport.width;

        // Netteté
        const outputScale = window.devicePixelRatio || 1;
        canvas.width = Math.floor(viewport.width * outputScale);
        canvas.height = Math.floor(viewport.height * outputScale);
        canvas.style.width = Math.floor(viewport.width) + "px";
        canvas.style.height = Math.floor(viewport.height) + "px";

        const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

        // AJOUT COUCHE PROTECTION (Empêche de glisser l'image sur le bureau)
        const protectionLayer = document.createElement('div');
        protectionLayer.className = 'protection-layer';

        // AJOUT WATERMARK (Optionnel - récupère le nom de l'user connecté s'il existe)
        const watermark = document.createElement('div');
        watermark.className = 'watermark';
        watermark.innerText = "Fasolivre Protected";

        wrapper.appendChild(canvas);
        wrapper.appendChild(protectionLayer);
        wrapper.appendChild(watermark);
        viewer.appendChild(wrapper);

        await page.render({
            canvasContext: ctx,
            transform: transform,
            viewport: viewport
        }).promise;
    }

    // Scroll & Save logic (Identique version précédente)
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) return;
        scrollTimeout = setTimeout(() => {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            let percent = Math.round((scrollTop / docHeight) * 100);
            percent = Math.min(100, Math.max(0, percent));
            document.getElementById('progressBar').style.width = percent + "%";
            window.currentProgress = percent;
            scrollTimeout = null;
        }, 100);
    });

    document.getElementById('saveProgressBtn').addEventListener('click', function() {
        const btn = this;
        const text = btn.querySelector('span');
        text.innerText = '...';
        fetch("{{ route('progress.update', $book->id) }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" },
            body: JSON.stringify({ progress: window.currentProgress || 0 })
        }).then(() => {
            btn.classList.add('bg-green-600'); text.innerText = 'OK';
            setTimeout(() => { btn.classList.remove('bg-green-600'); text.innerText = 'Sauver'; }, 2000);
        });
    });
</script>
@endsection
