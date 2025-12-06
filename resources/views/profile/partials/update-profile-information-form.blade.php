<section class="space-y-6">

    {{-- ==========================================
         HEADER
    =========================================== --}}
    <header class="border-l-4 border-[#E0551B] pl-4">
        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i data-lucide="user-circle" class="w-6 h-6 text-[#E0551B]"></i>
            Informations du profil
        </h2>

        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
            Mettez à jour vos informations personnelles et votre adresse email.
        </p>
    </header>


    {{-- FORMULAIRE VERIFICATION EMAIL --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>


    {{-- ==========================================
         FORMULAIRE PRINCIPAL
    =========================================== --}}
    <form method="post" action="{{ route('profile.update') }}"
          class="mt-6 space-y-6 bg-white p-6 rounded-2xl shadow-md border border-slate-200">

        @csrf
        @method('patch')

        {{-- =============================
             NOM
        ============================== --}}
        <div>
            <label for="name" class="font-medium text-sm text-slate-700">Nom complet</label>

            <div class="mt-1 relative">
                <input id="name" name="name" type="text"
                       value="{{ old('name', $user->name) }}"
                       required autocomplete="name"
                       class="w-full px-4 py-2 rounded-xl border border-slate-300
                              focus:ring-[#E0551B] focus:border-[#E0551B] transition"/>

                <i data-lucide="badge" class="absolute right-3 top-3 w-5 h-5 text-slate-400"></i>
            </div>

            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>


        {{-- =============================
             EMAIL
        ============================== --}}
        <div>
            <label for="email" class="font-medium text-sm text-slate-700">Adresse email</label>

            <div class="mt-1 relative">
                <input id="email" name="email" type="email"
                       value="{{ old('email', $user->email) }}"
                       required autocomplete="username"
                       class="w-full px-4 py-2 rounded-xl border border-slate-300
                              focus:ring-[#079C25] focus:border-[#079C25] transition"/>

                <i data-lucide="mail" class="absolute right-3 top-3 w-5 h-5 text-slate-400"></i>
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2"/>


            {{-- EMAIL NON VERIFY --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 bg-orange-50 border border-orange-200 p-4 rounded-xl">
                    <p class="text-sm text-orange-700 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        Votre adresse email n’est pas vérifiée.
                    </p>

                    <button form="send-verification"
                            class="mt-2 text-sm text-[#E0551B] underline hover:text-[#b84417] font-medium">
                        Renvoyer l’email de vérification
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-600 flex items-center gap-1">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Un nouveau lien de vérification a été envoyé.
                        </p>
                    @endif
                </div>
            @endif

        </div>


        {{-- =============================
             SAVE BUTTON + SUCCESS STATE
        ============================== --}}
        <div class="flex items-center gap-4">

            <button
                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#E0551B] to-[#079C25]
                       text-white font-semibold shadow hover:opacity-90 transition flex items-center gap-2">
                <i data-lucide="save" class="w-5 h-5"></i>
                Sauvegarder
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-600 flex items-center gap-1">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Modifié !
                </p>
            @endif
        </div>

    </form>

</section>


{{-- Lucide icons --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
