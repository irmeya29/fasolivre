<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;

class BookFrontController extends Controller
{
    public function index()
    {
        $q        = request('q');
        $access   = request('access');   // free | paid | subscription
        $category = request('category'); // slug
        $sort     = request('sort', 'new'); // new | old | price_asc | price_desc

        $booksQuery = Book::with(['author', 'category'])
            ->where('status', 'published');

        // Recherche : titre + auteur
        if (!empty($q)) {
            $booksQuery->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhereHas('author', function ($a) use ($q) {
                        $a->where('name', 'like', "%{$q}%");
                    });
            });
        }

        // Filtre access
        if (in_array($access, ['free', 'paid', 'subscription'], true)) {
            $booksQuery->where('access_type', $access);
        }

        // Filtre catégorie
        if (!empty($category)) {
            $booksQuery->whereHas('category', function ($c) use ($category) {
                $c->where('slug', $category);
            });
        }

        // Tri
        switch ($sort) {
            case 'old':
                $booksQuery->orderByRaw("COALESCE(published_at, created_at) ASC");
                break;

            case 'price_asc':
                $booksQuery->orderByRaw("CASE WHEN price IS NULL THEN 1 ELSE 0 END, price ASC");
                break;

            case 'price_desc':
                $booksQuery->orderByRaw("CASE WHEN price IS NULL THEN 1 ELSE 0 END, price DESC");
                break;

            case 'new':
            default:
                $booksQuery->orderByRaw("COALESCE(published_at, created_at) DESC");
                break;
        }

        $books = $booksQuery->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        return view('front.books.index', compact('books', 'categories'));
    }

    public function show($slug)
    {
        $book = Book::with(['author', 'category'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $related = Book::with(['author', 'category'])
            ->where('status', 'published')
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->latest()
            ->take(10)
            ->get();

        return view('front.books.show', compact('book', 'related'));
    }
}
