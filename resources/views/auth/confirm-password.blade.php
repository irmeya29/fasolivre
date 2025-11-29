<x-guest-layout title="Confirmation requise">

    <h1 class="text-xl font-semibold text-gray-900 mb-4">Confirmation requise</h1>

    <p class="text-sm text-gray-600 mb-6">
        Cette action nécessite la confirmation de votre mot de passe.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium">Mot de passe</label>
            <input type="password" name="password" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500">
        </div>

        <button class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700">
            Confirmer
        </button>

    </form>

</x-guest-layout>
