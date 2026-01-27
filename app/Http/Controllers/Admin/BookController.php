<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Affiche la liste des livres avec filtrage et recherche.
     */
    public function index(Request $request)
    {
        // 1. Initialisation de la requête avec Eager Loading (optimisation SQL)
        $query = Book::with(['author', 'category']);

        // 2. Recherche par Mots-clés (Titre ou Auteur)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('author', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Filtre par Catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 4. Filtre par Statut (Publié / Brouillon)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 5. Exécution : Tri par date décroissante et Pagination
        // on utilise withQueryString() pour garder les filtres actifs lors du changement de page
        $books = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        // Chargement des catégories pour le menu déroulant de filtre
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create()
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.create', compact('authors', 'categories'));
    }

    /**
     * Enregistre un nouveau livre.
     */
    public function store(Request $request)
    {
        // 1. Validation stricte
        $request->validate([
            'title'       => 'required|max:255',
            'author_id'   => 'required|exists:authors,id', // Author required for integrity
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',

            'format'      => 'required|in:pdf,audio,pdf_audio',
            'access_type' => 'required|in:free,paid,subscription',
            'price'       => 'nullable|numeric|min:0',
            'status'      => 'required|in:draft,published',

            // Validation des fichiers (MIME types et taille max en KO)
            'cover'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB
            'pdf_file'    => 'nullable|mimes:pdf|max:50000', // 50MB
            'audio_file'  => 'nullable|mimes:mp3,wav,ogg,m4a|max:100000', // 100MB
        ]);

        // 2. Gestion des uploads
        $coverPath = $request->hasFile('cover')
            ? $request->file('cover')->store('books/covers', 'public')
            : null;

        $pdfPath = $request->hasFile('pdf_file')
            ? $request->file('pdf_file')->store('books/pdf', 'public')
            : null;

        $audioPath = $request->hasFile('audio_file')
            ? $request->file('audio_file')->store('books/audio', 'public')
            : null;

        // 3. Création en base de données
        Book::create([
            'author_id'    => $request->author_id,
            'category_id'  => $request->category_id,
            'title'        => $request->title,
            'slug'         => Str::slug($request->title) . '-' . Str::random(4), // Slug unique
            'description'  => $request->description,

            'format'       => $request->format,
            'access_type'  => $request->access_type,
            // Si c'est gratuit ou abonnement, on force le prix à 0 pour éviter des erreurs
            'price'        => $request->access_type === 'paid' ? $request->price : 0,

            'cover'        => $coverPath,
            'pdf_file'     => $pdfPath,
            'audio_file'   => $audioPath,

            'status'       => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre ajouté avec succès au catalogue.');
    }

    /**
     * Affiche le formulaire d'édition.
     */
    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.edit', compact('book', 'authors', 'categories'));
    }

    /**
     * Met à jour un livre existant.
     */
    public function update(Request $request, Book $book)
    {
        // 1. Validation
        $request->validate([
            'title'       => 'required|max:255',
            'author_id'   => 'required|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',

            'format'      => 'required|in:pdf,audio,pdf_audio',
            'access_type' => 'required|in:free,paid,subscription',
            'price'       => 'nullable|numeric|min:0',
            'status'      => 'required|in:draft,published',

            'cover'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pdf_file'    => 'nullable|mimes:pdf|max:50000',
            'audio_file'  => 'nullable|mimes:mp3,wav,ogg,m4a|max:100000',
        ]);

        // 2. Gestion des fichiers (Suppression des anciens si nouveaux uploadés)

        // -- Cover --
        $coverPath = $book->cover;
        if ($request->hasFile('cover')) {
            // Supprimer l'ancienne image si elle existe
            if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $request->file('cover')->store('books/covers', 'public');
        }

        // -- PDF --
        $pdfPath = $book->pdf_file;
        if ($request->hasFile('pdf_file')) {
            if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
            }
            $pdfPath = $request->file('pdf_file')->store('books/pdf', 'public');
        }

        // -- Audio --
        $audioPath = $book->audio_file;
        if ($request->hasFile('audio_file')) {
            if ($audioPath && Storage::disk('public')->exists($audioPath)) {
                Storage::disk('public')->delete($audioPath);
            }
            $audioPath = $request->file('audio_file')->store('books/audio', 'public');
        }

        // 3. Mise à jour des données
        $book->update([
            'author_id'    => $request->author_id,
            'category_id'  => $request->category_id,
            'title'        => $request->title,
            // On ne met à jour le slug que si nécessaire, ou on le garde fixe pour le SEO
            'slug'         => Str::slug($request->title),
            'description'  => $request->description,

            'format'       => $request->format,
            'access_type'  => $request->access_type,
            'price'        => $request->access_type === 'paid' ? $request->price : 0,

            'cover'        => $coverPath,
            'pdf_file'     => $pdfPath,
            'audio_file'   => $audioPath,

            'status'       => $request->status,
            // Si on passe de draft à published, on met la date. Sinon on garde l'ancienne.
            'published_at' => ($request->status === 'published' && is_null($book->published_at))
                                ? now()
                                : $book->published_at,
        ]);

        return redirect()->route('admin.books.index')
            ->with('success', 'Informations du livre mises à jour.');
    }

    /**
     * Supprime un livre et ses fichiers associés.
     */
    public function destroy(Book $book)
    {
        // 1. Suppression physique des fichiers
        $files = [$book->cover, $book->pdf_file, $book->audio_file];

        foreach ($files as $file) {
            if ($file && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }

        // 2. Suppression en base
        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre et fichiers associés supprimés définitivement.');
    }
}
