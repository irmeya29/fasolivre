<x-guest-layout title="Connexion">

    <h1 class="text-xl font-semibold text-gray-900 mb-6">Connexion à votre compte</h1>

    {{-- Session Status --}}
    @if(session('status'))
        <div class="mb-4 p-3 text-sm text-green-700 bg-green-50 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Adresse email</label>
            <input type="email" name="email" required autofocus
                class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input type="password" name="password" required
                class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600">
                Se souvenir de moi
            </label>

            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                Mot de passe oublié ?
            </a>
        </div>

        <button
            class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">
            Se connecter
        </button>

        <p class="text-center text-sm mt-4">
            Pas encore inscrit ?
            <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:underline">
                Créer un compte
            </a>
        </p>
    </form>

</x-guest-layout>
