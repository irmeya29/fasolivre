@extends('front.layouts.app')

@section('title', 'Catégories – Fasolivre')

@section('content')

<style>
    :root {
        --faso-orange: #E0551B;
        --faso-green: #079C25;
        --faso-gold: #DCAE81;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        transition: 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.08);
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-12">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-10">
        <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-2">
            <i data-lucide="grid" class="w-7 h-7 text-[var(--faso-orange)]"></i>
            Explorer les catégories
        </h1>
    </div>


    {{-- GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8">

        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
               class="glass-card p-6 rounded-2xl text-center relative overflow-hidden">

                {{-- Halo --}}
                <div class="absolute -top-6 -right-6 w-20 h-20 bg-[var(--faso-orange)]/20 blur-2xl rounded-full"></div>

                {{-- Icon --}}
                <div class="w-14 h-14 mx-auto bg-[var(--faso-orange)]/10 rounded-2xl flex items-center justify-center">
                    <i data-lucide="folder" class="w-6 h-6 text-[var(--faso-orange)]"></i>
                </div>

                {{-- Category Name --}}
                <h3 class="mt-4 font-semibold text-slate-900 text-base truncate">
                    {{ $category->name }}
                </h3>

                {{-- Count --}}
                <p class="text-xs text-slate-500 mt-1">
                    {{ $category->books()->count() }} livres
                </p>

            </a>
        @endforeach

    </div>

    {{-- PAGINATION --}}
    <div class="mt-12">
        {{ $categories->links('pagination::tailwind') }}
    </div>

</div>

@endsection
