<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::orderBy('id', 'desc')->paginate(10);
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
            'photo'     => 'nullable|image|max:2048',
        ]);

        // Upload photo
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
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.authors.index')
            ->with('success', 'Auteur créé avec succès.');
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
            'photo'     => 'nullable|image|max:2048',
        ]);

        $photoPath = $author->photo;

        // Upload nouvelle photo
        if ($request->hasFile('photo')) {

            // Supprimer ancienne photo
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
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.authors.index')
            ->with('success', 'Auteur mis à jour.');
    }

    public function destroy(Author $author)
    {
        if ($author->photo && Storage::disk('public')->exists($author->photo)) {
            Storage::disk('public')->delete($author->photo);
        }

        $author->delete();

        return redirect()->route('admin.authors.index')
            ->with('success', 'Auteur supprimé.');
    }
}
