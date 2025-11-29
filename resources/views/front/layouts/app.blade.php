<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fasolivre')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Typography / smooth rendering --}}
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">

    {{-- HEADER --}}
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i data-lucide="book-open" class="w-6 h-6"></i>
                Fasolivre
            </a>

            {{-- Desktop Menu --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">

                <a href="{{ route('home') }}" class="hover:text-indigo-600 flex items-center gap-1">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    Accueil
                </a>

                <a href="{{ route('books.index') }}" class="hover:text-indigo-600 flex items-center gap-1">
                    <i data-lucide="library" class="w-4 h-4"></i>
                    Livres
                </a>

                <a href="{{ route('authors.index.front') }}" class="hover:text-indigo-600 flex items-center gap-1">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    Auteurs
                </a>

                <a href="{{ route('categories.index.front') }}" class="hover:text-indigo-600 flex items-center gap-1">
                    <i data-lucide="grid" class="w-4 h-4"></i>
                    Catégories
                </a>

                {{-- Search --}}
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                    <input type="text"
                        name="q"
                        placeholder="Rechercher..."
                        class="pl-9 pr-3 py-2 bg-gray-100 border rounded-lg focus:bg-white
                               focus:border-indigo-500 w-48">
                </form>

            </nav>

            {{-- Auth Buttons Desktop --}}
            <div class="hidden md:flex items-center gap-4">

                @auth
                    <a href="{{ route('account.index') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700
                               flex items-center gap-1">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        Mon compte
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="text-gray-700 hover:text-red-600 flex items-center gap-1">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Déconnexion
                        </button>
                    </form>

                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 flex items-center gap-1">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Connexion
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-1">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Inscription
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <button onclick="toggleMobileMenu()" class="md:hidden">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>

        {{-- MOBILE MENU --}}
        <div id="mobileMenu" class="md:hidden hidden bg-white border-t shadow-sm">
            <div class="p-4 space-y-3">

                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <i data-lucide="home" class="w-4 h-4"></i> Accueil
                </a>

                <a href="{{ route('books.index') }}" class="flex items-center gap-2">
                    <i data-lucide="library" class="w-4 h-4"></i> Livres
                </a>

                <a href="{{ route('authors.index.front') }}" class="flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4"></i> Auteurs
                </a>

                <a href="{{ route('categories.index.front') }}" class="flex items-center gap-2">
                    <i data-lucide="grid" class="w-4 h-4"></i> Catégories
                </a>

                {{-- Mobile Search --}}
                <form action="{{ route('search') }}" method="GET" class="relative mt-3">
                    <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                    <input type="text" name="q"
                           placeholder="Rechercher..."
                           class="pl-9 pr-3 py-2 w-full bg-gray-100 border rounded-lg">
                </form>

                @auth
                    <a href="{{ route('account.index') }}" class="flex items-center gap-2 mt-2">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        Mon compte
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="flex items-center gap-2 mt-2">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Déconnexion
                        </button>
                    </form>

                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 mt-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Connexion
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center gap-2 mt-2">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Inscription
                    </a>
                @endauth

            </div>
        </div>
    </header>


    {{-- CONTENT --}}
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4">
            @yield('content')
        </div>
    </main>


    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-gray-300 mt-16 py-10">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">

            <div>
                <h3 class="text-white font-semibold mb-3">Fasolivre</h3>
                <p class="text-sm text-gray-400">
                    Plateforme africaine dédiée aux ebooks et auteurs du continent.
                </p>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-3">Navigation</h3>
                <ul class="space-y-2 text-sm">

                    <li><a href="{{ route('home') }}" class="hover:text-white flex items-center gap-2">
                        <i data-lucide="home" class="w-4 h-4"></i> Accueil
                    </a></li>

                    <li><a href="{{ route('books.index') }}" class="hover:text-white flex items-center gap-2">
                        <i data-lucide="library" class="w-4 h-4"></i> Livres
                    </a></li>

                    <li><a href="{{ route('authors.index.front') }}" class="hover:text-white flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4"></i> Auteurs
                    </a></li>

                    <li><a href="{{ route('categories.index.front') }}" class="hover:text-white flex items-center gap-2">
                        <i data-lucide="grid" class="w-4 h-4"></i> Catégories
                    </a></li>

                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-3">Compte</h3>
                <ul class="space-y-2 text-sm">

                    @auth
                        <li><a href="{{ route('account.index') }}" class="hover:text-white flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4"></i> Mon compte
                        </a></li>

                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="hover:text-red-400 flex items-center gap-2">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </li>

                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white flex items-center gap-2">
                            <i data-lucide="log-in" class="w-4 h-4"></i> Connexion
                        </a></li>

                        <li><a href="{{ route('register') }}" class="hover:text-white flex items-center gap-2">
                            <i data-lucide="user-plus" class="w-4 h-4"></i> Inscription
                        </a></li>
                    @endauth

                </ul>
            </div>

        </div>

        <p class="text-center text-xs text-gray-500 mt-10">
            © {{ date('Y') }} Fasolivre — Tous droits réservés.
        </p>
    </footer>


<script>
    lucide.createIcons();

    function toggleMobileMenu() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    }
</script>

</body>
</html>
