@extends('front.layouts.app')

@section('title', 'Écoute – ' . $book->title)

@section('content')

<style>
    /* Fond global un peu sombre pour faire ressortir le lecteur */
    body {
        background-color: #f3f4f6;
    }

    /* Carte principale */
    .player-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px -5px rgba(0,0,0,0.1);
        padding: 30px;
        position: relative;
        overflow: hidden;
    }

    /* Effet d'ombre colorée sous l'image */
    .cover-shadow {
        position: absolute;
        bottom: -10px; left: 10px; right: 10px;
        height: 90%;
        background-image: url('{{ asset("storage/".$book->cover) }}');
        background-size: cover;
        background-position: center;
        filter: blur(25px) saturate(1.5);
        opacity: 0.4;
        z-index: 0;
        border-radius: 20px;
    }

    .cover-img {
        position: relative;
        z-index: 10;
        border-radius: 16px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    /* Custom Range Slider (La barre de progression) */
    input[type=range] {
        -webkit-appearance: none;
        width: 100%;
        background: transparent;
        cursor: pointer;
        height: 6px;
    }

    /* Chrome/Safari Thumb */
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 16px;
        width: 16px;
        border-radius: 50%;
        background: #E0551B;
        margin-top: -5px; /* Centrer sur la track */
        box-shadow: 0 0 10px rgba(224, 85, 27, 0.4);
        transition: transform 0.1s;
    }
    input[type=range]::-webkit-slider-thumb:hover {
        transform: scale(1.2);
    }

    /* Track (La ligne grise) */
    input[type=range]::-webkit-slider-runnable-track {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 10px;
    }

    /* Animation loader */
    .spinner {
        border: 3px solid rgba(0,0,0,0.1);
        width: 24px; height: 24px;
        border-radius: 50%;
        border-left-color: #E0551B;
        animation: spin 1s linear infinite;
        display: inline-block;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>

<div class="min-h-screen flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-md">

        {{-- Header Retour --}}
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('books.show', $book->slug) }}"
               class="p-2 bg-white rounded-full shadow-sm hover:bg-gray-50 transition text-slate-600">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <span class="text-xs font-bold text-slate-400 tracking-widest uppercase">Lecteur Audio</span>
            <div class="w-9"></div> {{-- Spacer pour centrer --}}
        </div>

        {{-- Player Card --}}
        <div class="player-card">

            {{-- Zone Image --}}
            <div class="relative w-48 h-64 mx-auto mb-8">
                <div class="cover-shadow"></div>
                <img src="{{ asset('storage/'.$book->cover) }}" class="cover-img w-full h-full object-cover">
            </div>

            {{-- Infos Titre --}}
            <div class="text-center mb-8">
                <h1 class="text-xl font-bold text-slate-800 leading-tight mb-1">{{ $book->title }}</h1>
                <p class="text-sm text-slate-500 font-medium">{{ $book->author->name }}</p>
            </div>

            {{-- Loading State --}}
            <div id="loadingState" class="text-center mb-6">
                <div class="spinner"></div>
                <p class="text-xs text-slate-400 mt-2">Chargement sécurisé...</p>
            </div>

            {{-- Controls Container (Masqué pendant chargement) --}}
            <div id="playerControls" class="opacity-0 transition-opacity duration-500">

                {{-- Progress Slider --}}
                <div class="mb-2">
                    <input type="range" id="progressBar" value="0" min="0" max="100" step="0.1">
                </div>

                {{-- Time Labels --}}
                <div class="flex justify-between text-xs text-slate-400 font-medium font-mono mb-8">
                    <span id="currentTime">00:00</span>
                    <span id="totalTime">00:00</span>
                </div>

                {{-- Boutons de contrôle --}}
                <div class="flex items-center justify-between">

                    {{-- Vitesse --}}
                    <button id="speedBtn" class="text-xs font-bold text-slate-500 w-10 hover:text-[#E0551B] transition">
                        1x
                    </button>

                    {{-- Back 10s --}}
                    <button id="back10" class="p-2 text-slate-400 hover:text-slate-700 transition">
                        <i data-lucide="rotate-ccw" class="w-6 h-6"></i>
                    </button>

                    {{-- Play / Pause (Gros bouton) --}}
                    <button id="playPause" class="w-16 h-16 bg-[#E0551B] rounded-full text-white shadow-lg shadow-orange-500/30 flex items-center justify-center hover:scale-105 transition transform active:scale-95">
                        <i data-lucide="play" class="w-7 h-7 fill-current"></i>
                    </button>

                    {{-- Forward 10s --}}
                    <button id="forward10" class="p-2 text-slate-400 hover:text-slate-700 transition">
                        <i data-lucide="rotate-cw" class="w-6 h-6"></i>
                    </button>

                    {{-- Mute / Volume (Optionnel, icône simple) --}}
                    <button id="muteBtn" class="text-slate-400 hover:text-slate-700 w-10 flex justify-end">
                        <i data-lucide="volume-2" class="w-5 h-5"></i>
                    </button>
                </div>

            </div>
        </div>

        {{-- Note de sécurité --}}
        <p class="text-center text-[10px] text-slate-400 mt-6">
            Lecture sécurisée par Fasolivre • {{ auth()->user()->name }}
        </p>

    </div>
</div>

{{-- Audio invisible --}}
<audio id="audioElement"></audio>

<script>
    // Configuration
    const rawUrl = "{{ asset('storage/'.$book->audio_file) }}";
    const updateUrl = "{{ route('progress.update', $book->id) }}";
    const csrfToken = "{{ csrf_token() }}";
    let savedProgressPercent = {{ $progress }}; // Progression base de données (0 à 100)

    // Éléments DOM
    const audio = document.getElementById('audioElement');
    const playBtn = document.getElementById('playPause');
    const progressBar = document.getElementById('progressBar');
    const currentTimeEl = document.getElementById('currentTime');
    const totalTimeEl = document.getElementById('totalTime');
    const speedBtn = document.getElementById('speedBtn');
    const loadingState = document.getElementById('loadingState');
    const playerControls = document.getElementById('playerControls');

    // --- 1. CHARGEMENT SECURISE (BLOB) ---
    fetch(rawUrl)
        .then(response => response.blob())
        .then(blob => {
            const blobUrl = URL.createObjectURL(blob);
            audio.src = blobUrl;

            // Une fois l'audio prêt (métadonnées chargées)
            audio.onloadedmetadata = () => {
                loadingState.style.display = 'none';
                playerControls.classList.remove('opacity-0');

                // Calcul durée totale
                totalTimeEl.innerText = formatTime(audio.duration);

                // Restauration progression
                if (savedProgressPercent > 0) {
                    const savedTime = (savedProgressPercent / 100) * audio.duration;
                    audio.currentTime = savedTime;
                    updateUI(savedTime);
                }
            };
        })
        .catch(err => {
            loadingState.innerHTML = '<p class="text-red-500 text-xs">Erreur chargement audio</p>';
        });

    // --- 2. FONCTIONS UTILITAIRES ---

    // Convertir secondes en MM:SS ou HH:MM:SS
    function formatTime(seconds) {
        if (!seconds || isNaN(seconds)) return "00:00";

        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);

        const mDisplay = m < 10 ? "0" + m : m;
        const sDisplay = s < 10 ? "0" + s : s;

        if (h > 0) {
            return `${h}:${mDisplay}:${sDisplay}`;
        }
        return `${mDisplay}:${sDisplay}`;
    }

    function updateUI(currentTime) {
        const duration = audio.duration;
        const percent = (currentTime / duration) * 100;

        progressBar.value = percent;
        // Petit effet visuel : on colorie la partie gauche de la barre
        progressBar.style.background = `linear-gradient(to right, #E0551B ${percent}%, #e5e7eb ${percent}%)`;

        currentTimeEl.innerText = formatTime(currentTime);
    }

    // --- 3. CONTROLES UTILISATEUR ---

    // Play / Pause
    playBtn.onclick = () => {
        if (audio.paused) {
            audio.play();
            // On change l'icône en Pause (SVG code direct pour rapidité ou via Lucide)
            playBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="white" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>';
        } else {
            audio.pause();
            playBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="white" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>';
        }
    };

    // Navigation temporelle (+/- 10s)
    document.getElementById('back10').onclick = () => audio.currentTime = Math.max(0, audio.currentTime - 10);
    document.getElementById('forward10').onclick = () => audio.currentTime = Math.min(audio.duration, audio.currentTime + 10);

    // Speed Control (Cycle : 1x -> 1.25x -> 1.5x -> 2x -> 0.75x -> 1x)
    const speeds = [1, 1.25, 1.5, 2, 0.75];
    let speedIndex = 0;
    speedBtn.onclick = () => {
        speedIndex = (speedIndex + 1) % speeds.length;
        const newSpeed = speeds[speedIndex];
        audio.playbackRate = newSpeed;
        speedBtn.innerText = newSpeed + "x";
    };

    // Barre de progression (Drag & Drop)
    progressBar.addEventListener('input', function() {
        const seekTime = (this.value / 100) * audio.duration;
        audio.currentTime = seekTime;
        updateUI(seekTime);
    });

    // --- 4. LOGIQUE TEMPS REEL & SAUVEGARDE ---

    let lastSaveTime = 0;

    audio.ontimeupdate = () => {
        updateUI(audio.currentTime);

        // Sauvegarde auto toutes les 10 secondes ou si on change bcp
        if (Math.abs(audio.currentTime - lastSaveTime) > 10) {
            saveProgress();
        }
    };

    // Sauvegarde à la pause aussi
    audio.onpause = () => saveProgress();

    function saveProgress() {
        const percent = (audio.currentTime / audio.duration) * 100;
        lastSaveTime = audio.currentTime;

        fetch(updateUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ progress: percent })
        }).catch(err => console.error("Save failed", err));
    }
</script>

@endsection
