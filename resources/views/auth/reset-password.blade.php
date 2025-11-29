<x-guest-layout title="Nouveau mot de passe">

    <h1 class="text-xl font-semibold text-gray-900 mb-6">Réinitialiser le mot de passe</h1>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Adresse email</label>
            <input type="email" name="email" required value="{{ old('email', $request->email) }}"
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500">
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
            <input type="password" name="password" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500">
        </div>

        {{-- Confirm --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500">
        </div>

        <button class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700">
            Mettre à jour mon mot de passe
        </button>

    </form>

</x-guest-layout>
