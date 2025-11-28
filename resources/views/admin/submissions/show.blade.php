@extends('admin.layouts.app')

@section('title', 'Soumission : '.$submission->title)

@section('content')

<div class="bg-white p-6 rounded-xl shadow-sm border max-w-4xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">{{ $submission->title }}</h2>

    <div class="space-y-5">

        <div>
            <p class="text-gray-600 text-sm">Soumis par</p>
            <p class="font-medium">{{ $submission->user->name ?? 'Utilisateur supprimé' }}</p>
        </div>

        <div>
            <p class="text-gray-600 text-sm">Description</p>
            <p class="font-medium">{{ $submission->description }}</p>
        </div>

        <div>
            <p class="text-gray-600 text-sm">Fichier PDF</p>
            <a href="{{ asset('storage/'.$submission->pdf) }}"
               target="_blank"
               class="text-indigo-600 underline">
                Télécharger / Voir
            </a>
        </div>

        <div>
            <p class="text-gray-600 text-sm">Statut</p>

            @if($submission->status == 'pending')
                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">En attente</span>
            @elseif($submission->status == 'accepted')
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Acceptée</span>
            @else
                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Refusée</span>
            @endif
        </div>

    </div>

    <div class="mt-8 flex gap-3">

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
