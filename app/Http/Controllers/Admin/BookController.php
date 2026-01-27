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
        $query = Book::with(['author', 'category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $books = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

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
        $request->validate([
            'title'       => 'required|max:255',
            'author_id'   => 'required|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',

            'format'      => 'required|in:pdf,audio,pdf_audio',
            'access_type' => 'required|in:free,paid,subscription',
            'price'       => 'required_if:access_type,paid|nullable|numeric|min:0',
            'status'      => 'required|in:draft,published',

            'cover'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Fichiers obligatoires selon format
            'pdf_file'    => 'required_if:format,pdf,required_if:format,pdf_audio|nullable|mimes:pdf|max:50000',
            'audio_file'  => 'required_if:format,audio,required_if:format,pdf_audio|nullable|mimes:mp3,wav,ogg,m4a|max:100000',
        ]);

        $coverPath = $request->hasFile('cover')
            ? $request->file('cover')->store('books/covers', 'public')
            : null;

        $pdfPath = $request->hasFile('pdf_file')
            ? $request->file('pdf_file')->store('books/pdf', 'public')
            : null;

        $audioPath = $request->hasFile('audio_file')
            ? $request->file('audio_file')->store('books/audio', 'public')
            : null;

        Book::create([
            'author_id'    => $request->author_id,
            'category_id'  => $request->category_id,
            'title'        => $request->title,
            // ✅ Le Model gère déjà le slug. Si tu veux forcer ici, garde la version unique:
            'slug'         => Str::slug($request->title) . '-' . Str::random(4),
            'description'  => $request->description,

            'format'       => $request->format,
            'access_type'  => $request->access_type,
            'price'        => $request->access_type === 'paid' ? (float)$request->price : 0,

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
        $request->validate([
            'title'       => 'required|max:255',
            'author_id'   => 'required|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',

            'format'      => 'required|in:pdf,audio,pdf_audio',
            'access_type' => 'required|in:free,paid,subscription',
            'price'       => 'required_if:access_type,paid|nullable|numeric|min:0',
            'status'      => 'required|in:draft,published',

            'cover'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Si on change le format, et qu'on n’a pas déjà le fichier en DB,
            // alors il faut l’upload.
            'pdf_file'    => 'nullable|mimes:pdf|max:50000',
            'audio_file'  => 'nullable|mimes:mp3,wav,ogg,m4a|max:100000',
        ]);

        // Validation complémentaire selon format + fichiers existants
        if (in_array($request->format, ['pdf', 'pdf_audio'], true) && !$book->pdf_file && !$request->hasFile('pdf_file')) {
            return back()->withErrors(['pdf_file' => 'Le fichier PDF est requis pour ce format.'])->withInput();
        }
        if (in_array($request->format, ['audio', 'pdf_audio'], true) && !$book->audio_file && !$request->hasFile('audio_file')) {
            return back()->withErrors(['audio_file' => 'Le fichier audio est requis pour ce format.'])->withInput();
        }

        // -- Cover --
        $coverPath = $book->cover;
        if ($request->hasFile('cover')) {
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

        $book->update([
            'author_id'    => $request->author_id,
            'category_id'  => $request->category_id,
            'title'        => $request->title,
            // ✅ NE PAS toucher slug ici (le Model Book s'en charge quand title change)
            'description'  => $request->description,

            'format'       => $request->format,
            'access_type'  => $request->access_type,
            'price'        => $request->access_type === 'paid' ? (float)$request->price : 0,

            'cover'        => $coverPath,
            'pdf_file'     => $pdfPath,
            'audio_file'   => $audioPath,

            'status'       => $request->status,
            'published_at' => $request->status === 'published'
                ? ($book->published_at ?? now())
                : null,
        ]);

        return redirect()->route('admin.books.index')
            ->with('success', 'Informations du livre mises à jour.');
    }

    /**
     * Supprime un livre et ses fichiers associés.
     */
    public function destroy(Book $book)
    {
        $files = [$book->cover, $book->pdf_file, $book->audio_file];

        foreach ($files as $file) {
            if ($file && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre et fichiers associés supprimés définitivement.');
    }
}
