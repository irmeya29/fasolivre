<x-app-layout>

    {{-- HEADER PERSONNALISÉ --}}
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <i data-lucide="user-circle" class="w-7 h-7 text-[#E0551B]"></i>
            Mon Profil
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">

        <div class="max-w-5xl mx-auto space-y-10 px-4">

            {{-- ============================
                SECTION : INFORMATION PROFIL
            ============================= --}}
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sm:p-10">
                <h3 class="flex items-center gap-2 text-xl font-semibold text-slate-900 mb-4">
                    <i data-lucide="id-card" class="w-6 h-6 text-[#E0551B]"></i>
                    Informations du profil
                </h3>

                <p class="text-sm text-slate-600 mb-6">
                    Mettez à jour vos informations personnelles et votre adresse email.
                </p>

                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>


            {{-- ============================
                SECTION : MOT DE PASSE
            ============================= --}}
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sm:p-10">
                <h3 class="flex items-center gap-2 text-xl font-semibold text-slate-900 mb-4">
                    <i data-lucide="lock-keyhole" class="w-6 h-6 text-[#079C25]"></i>
                    Sécurité du compte
                </h3>

                <p class="text-sm text-slate-600 mb-6">
                    Changez votre mot de passe pour renforcer la sécurité de votre compte.
                </p>

                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>


            {{-- ============================
                SECTION : SUPPRESSION
            ============================= --}}
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sm:p-10">
                <h3 class="flex items-center gap-2 text-xl font-semibold text-red-600 mb-4">
                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                    Suppression du compte
                </h3>

                <p class="text-sm text-slate-600 mb-6">
                    Attention ! Cette action est définitive et supprimera toutes vos données.
                </p>

                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>

    {{-- ICONES Lucide --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script> lucide.createIcons(); </script>

</x-app-layout>
