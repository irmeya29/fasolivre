<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;

class BookFrontController extends Controller
{
    public function index()
    {
        $books = Book::with('author', 'category')
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('front.books.index', compact('books'));
    }

    public function show($slug)
    {
        $book = Book::with('author', 'category')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Suggestions : même catégorie
        $related = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->inRandomOrder()
            ->take(6)
            ->get();

        return view('front.books.show', compact('book', 'related'));
    }
}
