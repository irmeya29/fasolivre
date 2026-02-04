<x-guest-layout title="Connexion">

    {{-- Title --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Connexion</h1>
        <p class="text-sm text-slate-500">Accédez à votre espace Fasolivre</p>
    </div>

    {{-- Session Status --}}
    @if(session('status'))
        <div class="mb-4 p-3 text-sm text-emerald-700 bg-emerald-100 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    {{-- ✅ Erreurs globales --}}
    @if($errors->any())
        <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- ✅ IMPORTANT: permet de revenir à la page du livre --}}
        <input type="hidden" name="redirect" value="{{ request('redirect') }}">


        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Adresse email</label>
            <div class="relative">
                <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="email" name="email" required autofocus value="{{ old('email') }}"
                    class="w-full pl-10 pr-3 py-2.5 border rounded-xl bg-gray-50 focus:bg-white
                           focus:ring-2 focus:ring-[#E0551B] transition text-sm">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe</label>
            <div class="relative">
                <i data-lucide="lock" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" name="password" required
                    class="w-full pl-10 pr-3 py-2.5 border rounded-xl bg-gray-50 focus:bg-white
                           focus:ring-2 focus:ring-[#079C25] transition text-sm">
            </div>
        </div>

        {{-- Remember + Forgot --}}
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                       class="rounded border-gray-300 text-[#079C25] focus:ring-[#079C25]">
                Se souvenir de moi
            </label>

            <a href="{{ route('password.request') }}"
               class="text-[#E0551B] hover:underline">
               Mot de passe oublié ?
            </a>
        </div>

        {{-- Submit --}}
        <button
            class="w-full bg-gradient-to-r from-[#E0551B] to-[#079C25] text-white py-3 rounded-xl
                   font-medium shadow hover:opacity-90 transition flex items-center justify-center gap-2">
            <i data-lucide="log-in" class="w-5 h-5"></i>
            Se connecter
        </button>

        {{-- Register Link --}}
        <p class="text-center text-sm mt-4 text-slate-600">
            Pas encore inscrit ?
            <a href="{{ route('register') }}" class="font-medium text-[#E0551B] hover:underline">
                Créer un compte
            </a>
        </p>

    </form>

</x-guest-layout>
