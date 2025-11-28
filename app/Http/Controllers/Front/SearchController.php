<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));

        // Filtres
        $category = $request->input('category');
        $access = $request->input('access');
        $format = $request->input('format');
        $sort = $request->input('sort', 'recent');

        $query = Book::with('author')->where('status', 'published');

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($access) {
            $query->where('access_type', $access);
        }

        if ($format) {
            $query->where('format', $format);
        }

        // Tri
        switch ($sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'oldest': $query->orderBy('published_at', 'asc'); break;
            default: $query->orderBy('published_at', 'desc'); break;
        }

        $books = $query->paginate(20);

        // Auteurs correspondants
        $authors = !empty($q)
            ? Author::where('name', 'like', "%{$q}%")->limit(10)->get()
            : collect();

        $categories = Category::all();

        return view('front.search.index', compact('q', 'books', 'authors', 'categories'));
    }


    /**
     * AJAX Search (instant typing)
     */
    public function ajax(Request $request)
    {
        $q = trim($request->q);

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $books = Book::where('title', 'like', "%{$q}%")
            ->limit(6)
            ->get(['id', 'slug', 'title', 'cover']);

        return response()->json($books);
    }
}
