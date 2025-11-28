<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fasolivre Admin')</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Icônes (Heroicons) --}}
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        body {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="min-h-screen flex">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r shadow-sm hidden md:flex flex-col">

        <div class="px-6 py-6 flex items-center gap-3 border-b">
            <div class="w-10 h-10 bg-indigo-600 text-white flex items-center justify-center rounded-lg font-bold">
                F
            </div>
            <span class="font-semibold text-lg">Fasolivre Admin</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50
                      {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                <i data-feather="home" class="w-5"></i>
                Dashboard
            </a>

            <a href="{{ route('admin.books.index') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50
                      {{ request()->routeIs('admin.books.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                <i data-feather="book-open" class="w-5"></i>
                Livres
            </a>

            <a href="{{ route('admin.authors.index') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50
                      {{ request()->routeIs('admin.authors.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                <i data-feather="users" class="w-5"></i>
                Auteurs
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50
                      {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                <i data-feather="tag" class="w-5"></i>
                Catégories
            </a>

            <a href="{{ route('admin.submissions.index') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50
                      {{ request()->routeIs('admin.submissions.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700' }}">
                <i data-feather="file-text" class="w-5"></i>
                Soumissions
            </a>

        </nav>

        <div class="p-4 border-t">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="w-full bg-red-50 text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 flex items-center gap-2">
                    <i data-feather="log-out" class="w-5"></i>
                    Déconnexion
                </button>
            </form>
        </div>

    </aside>

    {{-- CONTENU PRINCIPAL --}}
    <main class="flex-1 p-6">

        {{-- Barre mobile --}}
        <div class="md:hidden flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 text-white flex items-center justify-center rounded-lg font-bold">
                    F
                </div>
                <span class="font-semibold">Fasolivre Admin</span>
            </div>
        </div>

        @yield('content')
    </main>

<script>
    feather.replace();
</script>

</body>
</html>
