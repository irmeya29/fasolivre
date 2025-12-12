<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administration') - Fasolivre</title>

    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js pour les interactions simples --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        /* Police Pro standard */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-900 antialiased">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

        {{-- SIDEBAR (Sombre pour le contraste) --}}
        <aside class="flex flex-col w-64 bg-slate-900 text-white transition-all duration-300 ease-in-out transform absolute z-30 h-full lg:relative lg:translate-x-0"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">

            {{-- Logo --}}
            <div class="flex items-center justify-center h-16 bg-slate-950 border-b border-slate-800 shadow-sm">
                <span class="text-xl font-bold tracking-wider uppercase text-indigo-400">Fasolivre</span>
            </div>

            {{-- Navigation --}}
            <div class="flex-1 overflow-y-auto py-4">
                <nav class="space-y-1 px-2">

                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors">
                        <i data-feather="grid" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                        Dashboard
                    </a>

                    <div class="mt-4 mb-2 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Catalogue
                    </div>

                    <a href="{{ route('admin.books.index') }}"
                       class="{{ request()->routeIs('admin.books.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors">
                        <i data-feather="book" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.books.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        Livres
                    </a>

                    <a href="{{ route('admin.authors.index') }}"
                       class="{{ request()->routeIs('admin.authors.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors">
                        <i data-feather="users" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.authors.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        Auteurs
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                       class="{{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors">
                        <i data-feather="tag" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        Catégories
                    </a>

                    <div class="mt-4 mb-2 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Gestion
                    </div>

                    <a href="{{ route('admin.submissions.index') }}"
                       class="{{ request()->routeIs('admin.submissions.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors">
                        <i data-feather="inbox" class="mr-3 h-5 w-5 {{ request()->routeIs('admin.submissions.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        Soumissions
                    </a>
                </nav>
            </div>

            {{-- Logout Footer --}}
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex w-full items-center px-2 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 rounded-md transition-colors">
                        <i data-feather="log-out" class="mr-3 h-5 w-5"></i>
                        Se déconnecter
                    </button>
                </form>
            </div>
        </aside>

        {{-- MOBILE OVERLAY --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 z-20 bg-black opacity-50 lg:hidden"></div>

        {{-- MAIN CONTENT WRAPPER --}}
        <div class="flex-1 flex flex-col overflow-hidden relative">

            {{-- HEADER --}}
            <header class="flex items-center justify-between h-16 bg-white shadow-sm px-6 border-b z-10">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden mr-4">
                        <i data-feather="menu"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">@yield('title')</h1>
                </div>

                <div class="flex items-center space-x-4">
                    {{-- User Dropdown simple --}}
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                            A
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden md:block">Admin User</span>
                    </div>
                </div>
            </header>

            {{-- SCROLLABLE CONTENT --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>

    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>
