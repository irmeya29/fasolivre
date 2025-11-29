<x-guest-layout title="Vérification email">

    <h1 class="text-xl font-semibold text-gray-900 mb-4">Vérifiez votre adresse email</h1>

    <p class="text-sm text-gray-600 mb-6">
        Merci de vous être inscrit !
        Avant de continuer, veuillez confirmer votre adresse email en cliquant sur le lien
        que nous venons de vous envoyer.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 text-sm bg-green-50 text-green-700 rounded">
            Un nouveau lien de vérification vient d’être envoyé.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
        @csrf
        <button class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 transition">
            Renvoyer un email de vérification
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button class="w-full py-2 rounded-lg text-gray-700 hover:text-indigo-600">
            Déconnexion
        </button>
    </form>

</x-guest-layout>
