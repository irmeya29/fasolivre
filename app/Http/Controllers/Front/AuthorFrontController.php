<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorFrontController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $authors = Author::query()
            ->where('is_active', 1)
            ->withCount('books') // ✅ pour afficher {{ $author->books_count }} dans la vue
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('bio', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('front.authors.index', compact('authors', 'q'));
    }

    public function show($slug)
    {
        $author = Author::query()
            ->where('is_active', 1)
            ->where('slug', $slug)
            ->with([
                'books' => function ($q) {
                    // ✅ si tu veux uniquement les livres publiés + ordre récent
                    $q->where('status', 'published')->latest();
                }
            ])
            ->withCount('books')
            ->firstOrFail();

        return view('front.authors.show', compact('author'));
    }
}
