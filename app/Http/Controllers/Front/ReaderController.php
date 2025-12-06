<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function read($slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        // Vérifier si l'utilisateur a le livre
        $pivot = $user->books()->where('book_id', $book->id)->first();

        // ------------------------------------------
        // 1️⃣ Si pas dans la bibliothèque :
        // ------------------------------------------
        if (!$pivot) {

            // 👉 Autoriser AUTO si livre gratuit
            if ($book->access_type === 'free') {

                $user->books()->attach($book->id, [
                    'progress' => 0,
                    'is_favorite' => false
                ]);

                $pivot = $user->books()->where('book_id', $book->id)->first();
            }
            else {
                return redirect()
                    ->route('books.show', $book->slug)
                    ->with('error', "Vous n’avez pas accès à ce livre.");
            }
        }

        // ------------------------------------------
        // 2️⃣ Récupérer progression
        // ------------------------------------------
        $progress = $pivot->pivot->progress ?? 0;

        return view('front.read.pdf', [
            'book'     => $book,
            'progress' => $progress
        ]);
    }
}
