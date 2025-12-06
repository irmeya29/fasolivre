<x-guest-layout title="Inscription">

    {{-- Title --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Créer un compte</h1>
        <p class="text-sm text-slate-500">Rejoignez la communauté Fasolivre</p>
    </div>

    {{-- FORM --}}
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nom complet</label>
            <div class="relative">
                <i data-lucide="user" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="name" required
                       class="w-full pl-10 pr-3 py-2.5 border rounded-xl bg-gray-50 focus:bg-white
                              focus:ring-2 focus:ring-[#E0551B] transition text-sm">
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Adresse email</label>
            <div class="relative">
                <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="email" name="email" required
                       class="w-full pl-10 pr-3 py-2.5 border rounded-xl bg-gray-50 focus:bg-white
                              focus:ring-2 focus:ring-[#079C25] transition text-sm">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe</label>
            <div class="relative">
                <i data-lucide="lock" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" name="password" required
                       class="w-full pl-10 pr-3 py-2.5 border rounded-xl bg-gray-50 focus:bg-white
                              focus:ring-2 focus:ring-[#E0551B] transition text-sm">
            </div>
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Confirmer le mot de passe</label>
            <div class="relative">
                <i data-lucide="check" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" name="password_confirmation" required
                       class="w-full pl-10 pr-3 py-2.5 border rounded-xl bg-gray-50 focus:bg-white
                              focus:ring-2 focus:ring-[#079C25] transition text-sm">
            </div>
        </div>

        {{-- Submit --}}
        <button
            class="w-full bg-gradient-to-r from-[#E0551B] to-[#079C25] text-white py-3 rounded-xl
                   font-medium shadow hover:opacity-90 transition flex items-center justify-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5"></i>
            S’inscrire
        </button>

        {{-- Login Link --}}
        <p class="text-center text-sm mt-4 text-slate-600">
            Déjà un compte ?
            <a href="{{ route('login') }}" class="font-medium text-[#E0551B] hover:underline">
                Se connecter
            </a>
        </p>

    </form>

</x-guest-layout>
