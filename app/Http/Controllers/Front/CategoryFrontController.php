<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Book;

class CategoryFrontController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->withCount('books') // ✅ évite books()->count() dans la vue
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('front.categories.index', compact('categories'));
    }

    public function show($slug)
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $books = Book::with(['author', 'category'])
            ->where('status', 'published')
            ->where('category_id', $category->id)
            ->orderByRaw("COALESCE(published_at, created_at) DESC")
            ->paginate(12)
            ->withQueryString();

        return view('front.categories.show', compact('category', 'books'));
    }
}
