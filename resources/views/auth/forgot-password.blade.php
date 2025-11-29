<x-guest-layout title="Mot de passe oublié">

    <h1 class="text-xl font-semibold text-gray-900 mb-6">Mot de passe oublié</h1>

    @if(session('status'))
        <div class="mb-4 p-3 text-sm text-green-700 bg-green-50 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Adresse email</label>
            <input type="email" name="email" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500">
        </div>

        <button class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 transition">
            Envoyer le lien de réinitialisation
        </button>

        <p class="text-center text-sm mt-4">
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">
                Retour à la connexion
            </a>
        </p>
    </form>

</x-guest-layout>
