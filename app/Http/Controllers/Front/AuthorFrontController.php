<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Author;

class AuthorFrontController extends Controller
{
    public function index()
    {
        $authors = Author::where('is_active', 1)
            ->orderBy('name')
            ->paginate(20);

        return view('front.authors.index', compact('authors'));
    }

    public function show($slug)
    {
        $author = Author::where('slug', $slug)
            ->with('books')
            ->firstOrFail();

        return view('front.authors.show', compact('author'));
    }
}
