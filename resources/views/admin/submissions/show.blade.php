@extends('admin.layouts.app')

@section('title', 'Soumission : '.$submission->title)

@section('content')

<div class="bg-white p-6 rounded-xl shadow-sm border max-w-4xl mx-auto">

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold">{{ $submission->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ $submission->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <div>
            @if($submission->status == 'pending')
                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">En attente</span>
            @elseif($submission->status == 'accepted')
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Acceptée</span>
            @else
                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Refusée</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="space-y-1">
            <p class="text-gray-600 text-sm">Soumis par</p>
            <p class="font-medium">
                {{ $submission->user->name ?? $submission->full_name ?? 'Utilisateur supprimé' }}
            </p>
        </div>

        <div class="space-y-1">
            <p class="text-gray-600 text-sm">Catégorie</p>
            <p class="font-medium">{{ $submission->category->name ?? '—' }}</p>
        </div>

        <div class="space-y-1">
            <p class="text-gray-600 text-sm">Téléphone</p>
            <p class="font-medium">
                {{ trim(($submission->phone_country_code ?? '').' '.($submission->phone_number ?? '')) ?: '—' }}
            </p>
        </div>

        <div class="space-y-1">
            <p class="text-gray-600 text-sm">Adresse</p>
            <p class="font-medium">
                @php
                    $addr = trim(($submission->address_line ?? '').', '.($submission->city ?? '').', '.($submission->country ?? ''));
                    $addr = trim($addr, " ,");
                @endphp
                {{ $addr ?: '—' }}
            </p>
        </div>

        <div class="md:col-span-2 space-y-1">
            <p class="text-gray-600 text-sm">Description</p>
            <p class="font-medium whitespace-pre-line">{{ $submission->description }}</p>
        </div>

        <div class="md:col-span-2 space-y-2">
            <p class="text-gray-600 text-sm">Fichier PDF</p>

            @if($submission->pdf)
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.submissions.download', $submission) }}"
                       class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 text-sm">
                        Télécharger
                    </a>

                    <a href="{{ asset('storage/'.$submission->pdf) }}"
                       target="_blank"
                       class="px-4 py-2 bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200 text-sm">
                        Ouvrir dans un nouvel onglet
                    </a>
                </div>
            @else
                <p class="text-gray-500">Aucun fichier.</p>
            @endif
        </div>

    </div>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('admin.submissions.edit', $submission) }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Modifier statut
        </a>

        <a href="{{ route('admin.submissions.index') }}"
           class="px-4 py-2 bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200">
            Retour
        </a>
    </div>

</div>

@endsection
