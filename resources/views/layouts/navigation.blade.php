<nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-semibold text-indigo-600">
            <i data-lucide="book-open"></i>
            Fasolivre
        </a>

        {{-- Desktop menu --}}
        <div class="hidden md:flex items-center gap-6">

            {{-- Lien compte --}}
            <a href="{{ route('account.index') }}"
               class="flex items-center gap-1 text-sm text-gray-700 hover:text-indigo-600">
                <i data-lucide="user"></i>
                Mon compte
            </a>

            {{-- Profil --}}
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-1 text-sm text-gray-700 hover:text-indigo-600">
                <i data-lucide="settings"></i>
                Profil
            </a>

            {{-- Déconnexion --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    class="flex items-center gap-1 text-sm text-red-600 hover:text-red-700">
                    <i data-lucide="log-out"></i>
                    Déconnexion
                </button>
            </form>
        </div>

        {{-- Mobile Button --}}
        <button onclick="document.getElementById('mobileMenu').classList.toggle('hidden')"
                class="md:hidden">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>

    </div>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="hidden bg-white border-t md:hidden px-4 py-3 space-y-3">

        <a href="{{ route('account.index') }}" class="flex items-center gap-2 text-gray-700">
            <i data-lucide="user"></i>
            Mon compte
        </a>

        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-gray-700">
            <i data-lucide="settings"></i>
            Profil
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="flex items-center gap-2 text-red-600">
                <i data-lucide="log-out"></i>
                Déconnexion
            </button>
        </form>

    </div>

    <script>
        lucide.createIcons();
    </script>
</nav>
