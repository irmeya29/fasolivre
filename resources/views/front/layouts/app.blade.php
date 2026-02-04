<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fasolivre')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Global Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        body {
            font-family: "Inter", system-ui, sans-serif;
            scroll-behavior: smooth;
        }

        /* Couleurs Fasolivre */
        :root {
            --faso-orange: #E0551B;
            --faso-green: #079C25;
            --faso-gold:  #DCAE81;
            --faso-dark:  #3E3E3E;
        }

        /* Header glassmorphism */
        .header-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Smooth transitions (light) */
        a, button, input {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--faso-orange);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--faso-green);
        }

        /* Hover effects */
        .nav-link {
            position: relative;
            padding-bottom: 4px;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--faso-orange);
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }

        /* Mobile menu animation */
        #mobileMenu {
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo hover effect */
        .logo-hover {
            transition: transform 0.3s ease;
        }
        .logo-hover:hover {
            transform: scale(1.03);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">

    <!-- HEADER MODERNE -->
    <header class="header-glass sticky top-0 z-50 border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 lg:px-6">
            <div class="flex justify-between items-center h-20">

                <!-- Logo (image intégrée) -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 logo-hover group">
                    <img
                        src="{{ asset('assets/branding/fasolivre-logo.png') }}"
                       class="h-[150px] w-auto object-contain" alt="Fasolivre">


                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="nav-link flex items-center gap-2 text-gray-700 hover:text-[var(--faso-orange)] font-medium">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span>Accueil</span>
                    </a>

                    <a href="{{ route('books.index') }}" class="nav-link flex items-center gap-2 text-gray-700 hover:text-[var(--faso-orange)] font-medium">
                        <i data-lucide="library" class="w-4 h-4"></i>
                        <span>Livres</span>
                    </a>

                    <a href="{{ route('authors.index.front') }}" class="nav-link flex items-center gap-2 text-gray-700 hover:text-[var(--faso-orange)] font-medium">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Auteurs</span>
                    </a>

                    <a href="{{ route('categories.index.front') }}" class="nav-link flex items-center gap-2 text-gray-700 hover:text-[var(--faso-orange)] font-medium">
                        <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                        <span>Catégories</span>
                    </a>
                </nav>

                <!-- Search & Auth -->
                <div class="hidden lg:flex items-center gap-4">
                    <!-- Search -->
                    <form action="{{ route('search') }}" method="GET" class="relative group">
                        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[var(--faso-orange)]"></i>
                        <input type="text"
                               name="q"
                               placeholder="Rechercher..."
                               class="pl-11 pr-4 py-2.5 bg-gray-100 border-2 border-transparent rounded-xl w-64
                                      focus:bg-white focus:border-[var(--faso-orange)] focus:outline-none
                                      placeholder:text-gray-400">
                    </form>

                    @auth
                        <a href="{{ route('account.index') }}"
                           class="flex items-center gap-2 px-5 py-2.5 bg-[var(--faso-green)] text-white rounded-xl
                                  hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 font-medium">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span>Mon compte</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="flex items-center gap-2 text-gray-700 hover:text-red-600 font-medium">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>

                    @else
                        <a href="{{ route('login') }}"
                           class="text-gray-700 hover:text-[var(--faso-orange)] font-medium">
                            Connexion
                        </a>

                        <!-- Inscription : couleur unique (plus de dégradé) -->
                        <a href="{{ route('register') }}"
                           class="px-5 py-2.5 bg-[var(--faso-orange)] text-white rounded-xl font-medium
                                  hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                            Inscription
                        </a>
                    @endauth
                </div>

                <!-- Mobile Button -->
                <button onclick="toggleMobileMenu()" class="lg:hidden p-2 hover:bg-gray-100 rounded-lg">
                    <i data-lucide="menu" class="w-6 h-6 text-gray-700"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="lg:hidden hidden border-t border-gray-100 bg-white">
            <div class="max-w-7xl mx-auto px-4 py-6 space-y-4">

                <!-- Search Mobile -->
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text"
                           name="q"
                           placeholder="Rechercher un livre..."
                           class="pl-11 pr-4 py-3 w-full bg-gray-100 border-2 border-transparent rounded-xl
                                  focus:bg-white focus:border-[var(--faso-orange)] focus:outline-none">
                </form>

                <div class="space-y-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 rounded-lg font-medium text-gray-700">
                        <i data-lucide="home" class="w-5 h-5"></i>
                        <span>Accueil</span>
                    </a>

                    <a href="{{ route('books.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 rounded-lg font-medium text-gray-700">
                        <i data-lucide="library" class="w-5 h-5"></i>
                        <span>Livres</span>
                    </a>

                    <a href="{{ route('authors.index.front') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 rounded-lg font-medium text-gray-700">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span>Auteurs</span>
                    </a>

                    <a href="{{ route('categories.index.front') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 rounded-lg font-medium text-gray-700">
                        <i data-lucide="grid-3x3" class="w-5 h-5"></i>
                        <span>Catégories</span>
                    </a>
                </div>

                @auth
                    <div class="pt-4 border-t border-gray-100 space-y-1">
                        <a href="{{ route('account.index') }}" class="flex items-center gap-3 px-4 py-3 bg-[var(--faso-green)] text-white rounded-lg font-medium hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            <span>Mon compte</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="flex items-center gap-3 px-4 py-3 w-full text-left hover:bg-red-50 rounded-lg font-medium text-red-600">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>

                @else
                    <div class="pt-4 border-t border-gray-100 space-y-2">
                        <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-3 border-2 border-gray-200 hover:border-[var(--faso-orange)] rounded-lg font-medium text-gray-700">
                            <i data-lucide="log-in" class="w-5 h-5"></i>
                            <span>Connexion</span>
                        </a>

                        <!-- Inscription mobile : couleur unique -->
                        <a href="{{ route('register') }}"
                           class="flex items-center gap-3 px-4 py-3 bg-[var(--faso-orange)] text-white rounded-lg font-medium
                                  hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                            <span>Inscription</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>


    <!-- CONTENT -->
    <main class="min-h-screen py-8 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-6">
            @yield('content')
        </div>
    </main>

    <!-- FOOTER MODERNE -->
    <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-gray-300 mt-20">
        <div class="max-w-7xl mx-auto px-4 lg:px-6 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">

                <!-- About -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img
                            src="{{ asset('assets/branding/fasolivre-logo.png') }}"
                            alt="Fasolivre"
                            class="h-[150px] w-auto"
                        />

                    </div>
                    <p class="text-gray-400 leading-relaxed mb-6 max-w-md">
                        L'univers numérique africain du livre, dédié aux auteurs,
                        lecteurs et passionnés de littérature. Découvrez, partagez
                        et célébrez la richesse de la culture africaine.
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-[var(--faso-orange)] rounded-lg flex items-center justify-center transition">
                            <i data-lucide="facebook" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-[var(--faso-orange)] rounded-lg flex items-center justify-center transition">
                            <i data-lucide="twitter" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-[var(--faso-orange)] rounded-lg flex items-center justify-center transition">
                            <i data-lucide="instagram" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-[var(--faso-orange)] rounded-lg flex items-center justify-center transition">
                            <i data-lucide="linkedin" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>

                <!-- Navigation -->
                <div>
                    <h3 class="text-white font-semibold text-lg mb-4">Navigation</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('home') }}" class="flex items-center gap-2 hover:text-[var(--faso-orange)] hover:translate-x-1">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                <span>Accueil</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('books.index') }}" class="flex items-center gap-2 hover:text-[var(--faso-orange)] hover:translate-x-1">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                <span>Livres</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('authors.index.front') }}" class="flex items-center gap-2 hover:text-[var(--faso-orange)] hover:translate-x-1">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                <span>Auteurs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('categories.index.front') }}" class="flex items-center gap-2 hover:text-[var(--faso-orange)] hover:translate-x-1">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                <span>Catégories</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Compte -->
                <div>
                    <h3 class="text-white font-semibold text-lg mb-4">Compte</h3>
                    <ul class="space-y-3">
                        @auth
                            <li>
                                <a href="{{ route('account.index') }}" class="flex items-center gap-2 hover:text-[var(--faso-green)] hover:translate-x-1">
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                    <span>Mon compte</span>
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="flex items-center gap-2 hover:text-red-400 hover:translate-x-1">
                                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                        <span>Déconnexion</span>
                                    </button>
                                </form>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('login') }}" class="flex items-center gap-2 hover:text-[var(--faso-orange)] hover:translate-x-1">
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                    <span>Connexion</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}" class="flex items-center gap-2 hover:text-[var(--faso-orange)] hover:translate-x-1">
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                    <span>Inscription</span>
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>

            <!-- Bottom -->
            <div class="pt-8 border-t border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} Fasolivre — Tous droits réservés.
                </p>
                <div class="flex gap-6 text-sm">
                    <a href="#" class="hover:text-[var(--faso-orange)]">Mentions légales</a>
                    <a href="#" class="hover:text-[var(--faso-orange)]">Confidentialité</a>
                    <a href="#" class="hover:text-[var(--faso-orange)]">Contact</a>
                </div>
            </div>
        </div>
    </footer>


    <script>
        // Initialiser les icônes Lucide
        lucide.createIcons();

        // Toggle mobile menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
            setTimeout(() => lucide.createIcons(), 100);
        }

        // Fermer le menu mobile lors du clic sur un lien
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu').classList.add('hidden');
            });
        });
    </script>

</body>
</html>
