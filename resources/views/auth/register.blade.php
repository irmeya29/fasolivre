<x-guest-layout title="Inscription">

    <h1 class="text-xl font-semibold text-gray-900 mb-6">Créer un compte</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Nom complet</label>
            <input type="text" name="name" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Adresse email</label>
            <input type="email" name="email" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input type="password" name="password" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Confirm --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <button
            class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">
            S’inscrire
        </button>

        <p class="text-center text-sm mt-4">
            Déjà un compte ?
            <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">
                Se connecter
            </a>
        </p>
    </form>

</x-guest-layout>
