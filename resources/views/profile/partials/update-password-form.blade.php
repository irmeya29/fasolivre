<section class="space-y-6">

    {{-- ==========================================
         HEADER
    =========================================== --}}
    <header class="border-l-4 border-[#E0551B] pl-4">
        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i data-lucide="lock" class="w-6 h-6 text-[#E0551B]"></i>
            Modifier le mot de passe
        </h2>

        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
            Utilisez un mot de passe <strong class="text-[#E0551B]">long et sécurisé</strong> pour protéger votre compte.
        </p>
    </header>


    {{-- ==========================================
         FORMULAIRE
    =========================================== --}}
    <form method="post"
          action="{{ route('password.update') }}"
          class="mt-6 space-y-6 bg-white p-6 rounded-2xl shadow-md border border-slate-200">

        @csrf
        @method('put')

        {{-- Password actuel --}}
        <div>
            <label for="update_password_current_password" class="font-medium text-sm text-slate-700">
                Mot de passe actuel
            </label>

            <div class="mt-1 relative">
                <input id="update_password_current_password"
                       name="current_password"
                       type="password"
                       autocomplete="current-password"
                       class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-[#E0551B] focus:border-[#E0551B] transition" />

                <i data-lucide="shield" class="absolute right-3 top-3 w-5 h-5 text-slate-400"></i>
            </div>

            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>


        {{-- Nouveau mot de passe --}}
        <div>
            <label for="update_password_password" class="font-medium text-sm text-slate-700">
                Nouveau mot de passe
            </label>

            <div class="mt-1 relative">
                <input id="update_password_password"
                       name="password"
                       type="password"
                       autocomplete="new-password"
                       class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-[#E0551B] focus:border-[#E0551B] transition" />

                <i data-lucide="key" class="absolute right-3 top-3 w-5 h-5 text-slate-400"></i>
            </div>

            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>


        {{-- Confirmation --}}
        <div>
            <label for="update_password_password_confirmation" class="font-medium text-sm text-slate-700">
                Confirmer le mot de passe
            </label>

            <div class="mt-1 relative">
                <input id="update_password_password_confirmation"
                       name="password_confirmation"
                       type="password"
                       autocomplete="new-password"
                       class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-[#079C25] focus:border-[#079C25] transition" />

                <i data-lucide="check-circle" class="absolute right-3 top-3 w-5 h-5 text-slate-400"></i>
            </div>

            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>


        {{-- ==========================================
             SAVE BUTTON + SUCCESS MESSAGE
        =========================================== --}}
        <div class="flex items-center gap-4">

            <button
                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#E0551B] to-[#079C25] text-white font-semibold shadow hover:opacity-90 transition flex items-center gap-2">
                <i data-lucide="save" class="w-5 h-5"></i>
                Sauvegarder
            </button>

            @if (session('status') === 'password-updated')
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



{{-- Icons --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
