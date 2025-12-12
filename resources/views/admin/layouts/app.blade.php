<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administration') - Fasolivre</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js (Core) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    {{-- Feather Icons --}}
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        /* Typographie Pro */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Scrollbar custom pour Webkit */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="h-full antialiased text-gray-900 bg-gray-50">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

        {{-- SIDEBAR --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-xl"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">

            {{-- Header Sidebar --}}
            <div class="flex h-16 shrink-0 items-center px-6 bg-slate-950 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-indigo-600 rounded-lg">
                        <i data-feather="book-open" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="text-lg font-bold tracking-wide text-gray-100">Fasolivre</span>
                </div>
                {{-- Close Mobile --}}
                <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-slate-400 hover:text-white">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-1">

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-feather="grid" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    Tableau de bord
                </a>

                {{-- Section Catalogue --}}
                <div class="pt-6 pb-2 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Catalogue</div>

                <a href="{{ route('admin.books.index') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.books.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-feather="book" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.books.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    Livres
                </a>

                <a href="{{ route('admin.authors.index') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.authors.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-feather="users" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.authors.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    Auteurs
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-feather="tag" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    Catégories
                </a>

                {{-- Section Gestion --}}
                <div class="pt-6 pb-2 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Gestion</div>

                <a href="{{ route('admin.submissions.index') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.submissions.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-feather="inbox" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.submissions.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    Soumissions
                </a>

            </nav>

            {{-- Footer User (Mobile only mainly, or sticky bottom) --}}
            <div class="border-t border-slate-800 p-4 bg-slate-950/50">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                        A
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">Administrateur</p>
                        <p class="text-xs text-slate-400 truncate">admin@fasolivre.com</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- OVERLAY MOBILE --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden"></div>

        {{-- MAIN AREA --}}
        <div class="flex flex-1 flex-col overflow-hidden bg-gray-50">

            {{-- TOPBAR --}}
            <header class="flex h-16 items-center justify-between gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 z-10">

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                        <span class="sr-only">Ouvrir sidebar</span>
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>

                    {{-- Search Bar (Visuelle) --}}
                    <div class="relative flex flex-1 items-center">
                        <i data-feather="search" class="absolute left-0 h-5 w-5 text-gray-400 pointer-events-none"></i>
                        <input type="text" placeholder="Rechercher partout..." class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 bg-transparent sm:text-sm">
                    </div>
                </div>

                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    {{-- Notifications --}}
                    <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 relative">
                        <span class="sr-only">Voir notifications</span>
                        <i data-feather="bell" class="h-6 w-6"></i>
                        <span class="absolute top-2 right-2.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    {{-- Separator --}}
                    <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                    {{-- User Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5" id="user-menu-button">
                            <span class="sr-only">Menu utilisateur</span>
                            <img class="h-8 w-8 rounded-full bg-gray-50" src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff" alt="">
                            <span class="hidden lg:flex lg:items-center">
                                <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">Admin User</span>
                                <i data-feather="chevron-down" class="ml-2 h-4 w-4 text-gray-400"></i>
                            </span>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                             style="display: none;">

                            <a href="#" class="block px-4 py-2 text-sm leading-6 text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <i data-feather="user" class="w-4 h-4"></i> Mon Profil
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm leading-6 text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <i data-feather="settings" class="w-4 h-4"></i> Paramètres
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm leading-6 text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <i data-feather="log-out" class="w-4 h-4"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 overflow-y-auto py-8">
                <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- TOAST NOTIFICATIONS (Success/Error) --}}
    @if(session('success') || session('error'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 4000)"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-5 right-5 z-50 flex w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">

        <div class="p-4 flex items-start">
            <div class="flex-shrink-0">
                @if(session('success'))
                    <i data-feather="check-circle" class="h-6 w-6 text-green-400"></i>
                @else
                    <i data-feather="alert-circle" class="h-6 w-6 text-red-400"></i>
                @endif
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900">
                    {{ session('success') ? 'Succès' : 'Erreur' }}
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    {{ session('success') ?? session('error') }}
                </p>
            </div>
            <div class="ml-4 flex flex-shrink-0">
                <button @click="show = false" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Fermer</span>
                    <i data-feather="x" class="h-5 w-5"></i>
                </button>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Init Feather Icons
        feather.replace();
    </script>
</body>
</html>
