<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Fasolivre' }}</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Alpine --}}
    <script src="https://unpkg.com/alpinejs" defer></script>

    <style>
        body { background: #f9fafb; }
    </style>
</head>

<body class="font-sans antialiased">

    <div class="min-h-screen">

        {{-- NAVIGATION BREEZE (profil, déconnexion, etc.) --}}
        @include('layouts.navigation')

        {{-- Header Breeze --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Page Content --}}
        <main class="py-10 max-w-7xl mx-auto px-4">
            {{ $slot }}
        </main>

    </div>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>
