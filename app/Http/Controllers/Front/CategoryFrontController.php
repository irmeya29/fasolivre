<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryFrontController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')
            ->paginate(20); // Pagination obligatoire pour links()

        return view('front.categories.index', compact('categories'));
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->firstOrFail();

        // Charger les livres de cette catégorie
        $books = $category->books()
            ->where('status', 'published')
            ->paginate(20);

        return view('front.categories.show', compact('category', 'books'));
    }
}
