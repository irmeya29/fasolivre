@extends('front.layouts.app')

@section('title', 'Catégories – Fasolivre')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-semibold text-slate-900 mb-8 flex items-center gap-2">
        <i data-lucide="grid" class="w-6 h-6 text-indigo-600"></i>
        Catégories
    </h1>

    {{-- GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
               class="bg-white p-5 rounded-xl shadow hover:shadow-lg transition text-center">

                <div class="w-12 h-12 mx-auto bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="folder" class="w-6 h-6 text-indigo-600"></i>
                </div>

                <h3 class="mt-4 font-semibold text-slate-900">{{ $category->name }}</h3>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $category->books()->count() }} livres
                </p>
            </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $categories->links('pagination::tailwind') }}
    </div>

</div>

@endsection
