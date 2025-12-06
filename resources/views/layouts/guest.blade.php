<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Authentification' }} – Fasolivre</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine --}}
    <script src="https://unpkg.com/alpinejs" defer></script>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #E0551B10 0%, #079C2510 100%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-8 border border-white/30">

            {{-- Logo --}}
            <div class="text-center mb-6">
                <a href="/" class="flex items-center justify-center gap-2 text-2xl font-bold
                     bg-gradient-to-r from-[#E0551B] to-[#079C25] bg-clip-text text-transparent">
                    <i data-lucide="book-open" class="w-7 h-7"></i>
                    Fasolivre
                </a>
            </div>

            {{ $slot }}

        </div>

        {{-- Footer --}}
        <p class="text-center text-gray-500 text-xs mt-6">
            © {{ date('Y') }} Fasolivre – Tous droits réservés
        </p>
    </div>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script> lucide.createIcons(); </script>

</body>
</html>
