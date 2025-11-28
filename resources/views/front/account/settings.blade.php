@extends('front.layouts.app')

@section('title', 'Paramètres du compte')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-12">

    <h1 class="text-2xl font-semibold text-slate-900 mb-8 flex items-center gap-3">
        <i data-lucide="settings" class="w-7 h-7 text-indigo-600"></i>
        Paramètres du compte
    </h1>

    {{-- Form --}}
    <div class="bg-white border shadow rounded-2xl p-8">

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Name --}}
            <div>
                <label class="text-sm font-medium text-slate-700">Nom complet</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}"
                       class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            {{-- Email --}}
            <div>
                <label class="text-sm font-medium text-slate-700">Adresse email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}"
                       class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            {{-- Password --}}
            <div>
                <label class="text-sm font-medium text-slate-700">Nouveau mot de passe</label>
                <input type="password" name="password"
                       class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                       placeholder="Laisser vide pour ne pas changer">
            </div>

            {{-- Password Confirm --}}
            <div>
                <label class="text-sm font-medium text-slate-700">Confirmer mot de passe</label>
                <input type="password" name="password_confirmation"
                       class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            <button class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 text-sm">
                Mettre à jour
            </button>

        </form>

    </div>

</div>

@endsection
