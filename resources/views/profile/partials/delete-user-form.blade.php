<section class="space-y-6">

    {{-- =======================================================
         HEADER
    ======================================================== --}}
    <header class="border-l-4 border-red-500 pl-4">
        <h2 class="text-xl font-bold text-red-600 flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            Suppression du compte
        </h2>

        <p class="mt-2 text-sm text-gray-600 leading-relaxed">
            Une fois votre compte supprimé, <strong class="text-red-600">toutes vos données seront perdues définitivement</strong>.
            Téléchargez vos informations si nécessaire avant de procéder.
        </p>
    </header>


    {{-- =======================================================
         DELETE BUTTON
    ======================================================== --}}
    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-semibold
               bg-red-600 text-white hover:bg-red-700 transition shadow-md">
        <i data-lucide="trash-2" class="w-5 h-5"></i>
        Supprimer mon compte
    </button>


    {{-- =======================================================
         MODAL CONFIRMATION
    ======================================================== --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>

        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6">
            @csrf
            @method('delete')

            {{-- Title --}}
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="shield-alert" class="w-6 h-6 text-red-600"></i>
                Confirmer la suppression
            </h2>

            {{-- Message --}}
            <p class="text-sm text-gray-600 leading-relaxed">
                Cette action est <strong class="text-red-600">irréversible</strong>.<br>
                Pour confirmer, veuillez entrer votre mot de passe.
            </p>

            {{-- Password --}}
            <div class="mt-4">
                <label for="password" class="text-sm font-medium text-gray-700">Mot de passe</label>

                <input id="password"
                       name="password"
                       type="password"
                       class="mt-1 w-full px-4 py-2 rounded-xl border-gray-300 focus:ring-red-500 focus:border-red-500"
                       placeholder="Votre mot de passe">

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex justify-end gap-3">

                {{-- Cancel --}}
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-medium">
                    Annuler
                </button>

                {{-- Delete --}}
                <button class="px-5 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white shadow text-sm font-semibold">
                    Supprimer définitivement
                </button>

            </div>

        </form>

    </x-modal>

</section>


{{-- Loader des icônes si besoin --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
