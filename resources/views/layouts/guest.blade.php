<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Authentification' }} – Fasolivre</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine --}}
    <script src="https://unpkg.com/alpinejs" defer></script>

    <style>
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #fafafa 100%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="bg-white/70 backdrop-blur-xl shadow-xl rounded-2xl p-8 border border-white/40">
            <div class="text-center mb-6">
                <a href="/" class="flex items-center justify-center gap-2 text-2xl font-bold text-indigo-600">
                    <i data-lucide="book-open"></i> Fasolivre
                </a>
            </div>

            {{ $slot }}

        </div>

        <p class="text-center text-gray-500 text-xs mt-6">
            © {{ date('Y') }} Fasolivre – Tous droits réservés
        </p>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script> lucide.createIcons(); </script>

</body>
</html>
