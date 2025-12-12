<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        // 1. On charge les auteurs avec le NOMBRE de livres associés (optimisation SQL)
        $query = Author::withCount('books');

        // 2. Recherche (Nom ou Bio)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        // 3. Filtre par Statut
        if ($request->filled('status')) {
            // Le formulaire envoie 'active' ou 'inactive', on convertit en booléen
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // 4. Pagination et Tri
        $authors = $query->orderBy('created_at', 'desc')
                         ->paginate(10)
                         ->withQueryString();

        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|max:255',
            'bio'       => 'nullable|string',
            'website'   => 'nullable|url',
            'facebook'  => 'nullable|url',
            'instagram' => 'nullable|url',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'sometimes|boolean' // 'sometimes' car les checkbox non cochées n'envoient rien
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('authors', 'public');
        }

        Author::create([
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
            'bio'       => $request->bio,
            'website'   => $request->website,
            'facebook'  => $request->facebook,
            'instagram' => $request->instagram,
            'photo'     => $photoPath,
            // Astuce : $request->has(...) renvoie true si la checkbox est cochée
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.authors.index')
            ->with('success', 'Auteur ajouté avec succès.');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name'      => 'required|max:255',
            'bio'       => 'nullable|string',
            'website'   => 'nullable|url',
            'facebook'  => 'nullable|url',
            'instagram' => 'nullable|url',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $photoPath = $author->photo;

        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('authors', 'public');
        }

        $author->update([
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
            'bio'       => $request->bio,
            'website'   => $request->website,
            'facebook'  => $request->facebook,
            'instagram' => $request->instagram,
            'photo'     => $photoPath,
            // Pour l'update, on vérifie si la clé existe dans la requête (checkbox)
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.authors.index')
            ->with('success', 'Profil auteur mis à jour.');
    }

    public function destroy(Author $author)
    {
        // Supprimer la photo si elle existe
        if ($author->photo && Storage::disk('public')->exists($author->photo)) {
            Storage::disk('public')->delete($author->photo);
        }

        $author->delete();

        return redirect()->route('admin.authors.index')
            ->with('success', 'Auteur supprimé définitivement.');
    }
}
