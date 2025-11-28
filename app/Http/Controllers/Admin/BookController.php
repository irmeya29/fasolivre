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
    public function index()
    {
        $books = Book::with(['author', 'category'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.create', compact('authors', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|max:255',
            'author_id'   => 'nullable|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',

            'format'       => 'required|in:pdf,audio,pdf_audio',
            'access_type'  => 'required|in:free,paid,subscription',
            'price'        => 'nullable|numeric|min:0',

            'cover'     => 'nullable|image|max:2048',
            'pdf_file'  => 'nullable|mimes:pdf|max:20000',
            'audio_file'=> 'nullable|mimes:mp3,wav,ogg|max:40000',
        ]);

        $coverPath = null;
        $pdfPath = null;
        $audioPath = null;

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $pdfPath = $request->file('pdf_file')->store('books/pdf', 'public');
        }

        if ($request->hasFile('audio_file')) {
            $audioPath = $request->file('audio_file')->store('books/audio', 'public');
        }

        Book::create([
            'author_id'   => $request->author_id,
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'description' => $request->description,

            'format'      => $request->format,
            'access_type' => $request->access_type,
            'price'       => $request->access_type == 'paid' ? $request->price : 0,

            'cover'       => $coverPath,
            'pdf_file'    => $pdfPath,
            'audio_file'  => $audioPath,

            'status'      => $request->status ?? 'draft',
            'published_at'=> $request->status == 'published' ? now() : null,
        ]);

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre créé avec succès.');
    }

    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.edit', compact('book', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'       => 'required|max:255',
            'author_id'   => 'nullable|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',

            'format'       => 'required|in:pdf,audio,pdf_audio',
            'access_type'  => 'required|in:free,paid,subscription',
            'price'        => 'nullable|numeric|min:0',

            'cover'      => 'nullable|image|max:2048',
            'pdf_file'   => 'nullable|mimes:pdf|max:20000',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg|max:40000',
        ]);

        $coverPath = $book->cover;
        $pdfPath = $book->pdf_file;
        $audioPath = $book->audio_file;

        if ($request->hasFile('cover')) {
            if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $request->file('cover')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
            }
            $pdfPath = $request->file('pdf_file')->store('books/pdf', 'public');
        }

        if ($request->hasFile('audio_file')) {
            if ($audioPath && Storage::disk('public')->exists($audioPath)) {
                Storage::disk('public')->delete($audioPath);
            }
            $audioPath = $request->file('audio_file')->store('books/audio', 'public');
        }

        $book->update([
            'author_id'   => $request->author_id,
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'description' => $request->description,

            'format'      => $request->format,
            'access_type' => $request->access_type,
            'price'       => $request->access_type == 'paid' ? $request->price : 0,

            'cover'       => $coverPath,
            'pdf_file'    => $pdfPath,
            'audio_file'  => $audioPath,

            'status'      => $request->status,
            'published_at'=> $request->status == 'published' ? now() : null,
        ]);

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre mis à jour avec succès.');
    }

    public function destroy(Book $book)
    {
        foreach (['cover', 'pdf_file', 'audio_file'] as $file) {
            if ($book->$file && Storage::disk('public')->exists($book->$file)) {
                Storage::disk('public')->delete($book->$file);
            }
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Livre supprimé.');
    }
}
