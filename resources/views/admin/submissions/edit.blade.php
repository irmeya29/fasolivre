@extends('admin.layouts.app')

@section('title', 'Modifier Soumission')

@section('content')

<div class="bg-white p-6 rounded-xl shadow-sm border max-w-xl mx-auto">

    <h2 class="text-xl font-semibold mb-4">Modifier le statut</h2>

    <form method="POST" action="{{ route('admin.submissions.update', $submission) }}">
        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="block mb-1 font-medium text-gray-700">Titre</label>
            <input type="text" value="{{ $submission->title }}" disabled
                   class="w-full p-3 bg-gray-100 border rounded-lg">
        </div>

        <div class="mb-5">
            <label class="block mb-1 font-medium text-gray-700">Statut</label>
            <select name="status" class="w-full p-3 border rounded-lg">

                <option value="pending" {{ $submission->status == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="accepted" {{ $submission->status == 'accepted' ? 'selected' : '' }}>Acceptée</option>
                <option value="rejected" {{ $submission->status == 'rejected' ? 'selected' : '' }}>Refusée</option>

            </select>
        </div>

        <div class="flex gap-3">
            <button class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Mettre à jour
            </button>

            <a href="{{ route('admin.submissions.index') }}"
               class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>

</div>

@endsection
