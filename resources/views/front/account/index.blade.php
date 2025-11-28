@extends('front.layouts.app')

@section('title', 'Mon compte')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-12">

    {{-- Title --}}
    <div class="mb-10">
        <h1 class="text-3xl font-semibold text-slate-900 flex items-center gap-3">
            <i data-lucide="user-circle" class="w-9 h-9 text-indigo-600"></i>
            Bonjour, {{ auth()->user()->name }}
        </h1>
        <p class="text-slate-500 text-sm mt-1">
            Accédez à vos lectures, vos paramètres et vos informations personnelles.
        </p>
    </div>


    {{-- CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Mes Livres --}}
        <a href="{{ route('account.books') }}"
           class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition flex flex-col gap-4">

            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i data-lucide="book-open" class="w-6 h-6 text-indigo-600"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-900">Mes livres</h2>
                <p class="text-sm text-slate-500">Voir les livres achetés ou gratuits.</p>
            </div>

        </a>

        {{-- Paramètres --}}
        <a href="{{ route('account.settings') }}"
           class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition flex flex-col gap-4">

            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i data-lucide="settings" class="w-6 h-6 text-indigo-600"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-900">Paramètres</h2>
                <p class="text-sm text-slate-500">Modifier les informations du compte.</p>
            </div>

        </a>

        {{-- Profil --}}
        <a href="{{ route('profile.edit') }}"
           class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition flex flex-col gap-4">

            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i data-lucide="user-cog" class="w-6 h-6 text-indigo-600"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-900">Mon Profil</h2>
                <p class="text-sm text-slate-500">Nom, avatar et informations personnelles.</p>
            </div>

        </a>

    </div>

</div>

@endsection
