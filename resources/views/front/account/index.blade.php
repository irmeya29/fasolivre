@extends('front.layouts.app')

@section('title', 'Mon compte – Fasolivre')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">



    {{-- ===========================================
         HEADER PREMIUM
    ============================================ --}}
    <div class="relative mb-12 bg-gradient-to-r from-[#E0551B] to-[#079C25] rounded-3xl p-8 text-white shadow-xl overflow-hidden">

        {{-- Effet texture --}}
        <div class="absolute inset-0 opacity-20"
             style="background-image:url('https://www.transparenttextures.com/patterns/asfalt-light.png');">
        </div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            {{-- Avatar + Infos --}}
            <div class="flex items-center gap-6">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ffffff&color=E0551B"
                     class="w-20 h-20 rounded-xl shadow-lg border-4 border-white object-cover">

                <div>
                    <h1 class="text-3xl font-bold">{{ auth()->user()->name }}</h1>
                    <p class="text-white/80 text-sm mt-1">Bienvenue dans votre espace Fasolivre 📚</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-6 text-center">

                <div>
                    <p class="text-2xl font-bold">{{ auth()->user()->books()->count() }}</p>
                    <p class="text-xs text-white/80">Livres possédés</p>
                </div>

                <div>
                    <p class="text-2xl font-bold">0</p>
                    <p class="text-xs text-white/80">Favoris</p>
                </div>

                <div>
                    <p class="text-2xl font-bold">∞</p>
                    <p class="text-xs text-white/80">Accès lectures</p>
                </div>

            </div>

        </div>
    </div>



    {{-- ===========================================
         LAYOUT : SIDEBAR + CONTENT
    ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">


        {{-- SIDEBAR --}}
        <aside class="space-y-3 lg:col-span-1">

            {{-- Dashboard --}}
            <a href="{{ route('account.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium
                      {{ request()->routeIs('account.index')
                        ? 'bg-gradient-to-r from-[#E0551B] to-[#079C25] text-white border-transparent shadow-lg'
                        : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Tableau de bord
            </a>

            {{-- Mes livres --}}
            <a href="{{ route('account.books') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium
                      {{ request()->routeIs('account.books')
                        ? 'bg-gradient-to-r from-[#E0551B] to-[#079C25] text-white border-transparent shadow-lg'
                        : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                Mes livres
            </a>

            {{-- Profil --}}
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium
                      {{ request()->routeIs('profile.edit')
                        ? 'bg-gradient-to-r from-[#E0551B] to-[#079C25] text-white border-transparent shadow-lg'
                        : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="user" class="w-5 h-5"></i>
                Profil & Sécurité
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 text-red-600 border border-red-200
                         hover:bg-red-100 cursor-pointer font-medium text-sm"
                  onclick="this.submit()">
                @csrf
                <i data-lucide="log-out" class="w-5 h-5"></i>
                Déconnexion
            </form>
        </aside>





        {{-- ===========================================
             ZONE CONTENU (Dashboard Cards)
        ============================================ --}}
        <main class="lg:col-span-3">

            <h2 class="text-xl font-semibold text-slate-800 mb-6">Navigation rapide</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">



                {{-- Card : Mes livres --}}
                <a href="{{ route('account.books') }}"
                   class="block bg-white p-6 rounded-2xl border border-slate-200 shadow-sm
                          hover:shadow-xl hover:-translate-y-1 transition">

                    <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-3 bg-[#E0551B]/10">
                        <i data-lucide="book-open" class="w-7 h-7 text-[#E0551B]"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-900">Mes livres</h3>
                    <p class="text-sm text-slate-500">Retrouvez toutes vos lectures.</p>
                </a>



                {{-- Card : Profil --}}
                <a href="{{ route('profile.edit') }}"
                   class="block bg-white p-6 rounded-2xl border border-slate-200 shadow-sm
                          hover:shadow-xl hover:-translate-y-1 transition">

                    <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-3 bg-[#079C25]/10">
                        <i data-lucide="settings" class="w-7 h-7 text-[#079C25]"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-900">Mon profil</h3>
                    <p class="text-sm text-slate-500">Modifier vos informations & sécurité.</p>
                </a>



                {{-- Card : Publier --}}
                <a href="{{ url('/submit') }}"
                   class="block bg-white p-6 rounded-2xl border border-slate-200 shadow-sm
                          hover:shadow-xl hover:-translate-y-1 transition">

                    <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-3 bg-[#DCAE81]/20">
                        <i data-lucide="file-plus" class="w-7 h-7 text-[#DCAE81]"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-900">Publier un manuscrit</h3>
                    <p class="text-sm text-slate-500">Soumettez vos œuvres à Fasolivre.</p>
                </a>

            </div>

        </main>

    </div>

</div>

@endsection
