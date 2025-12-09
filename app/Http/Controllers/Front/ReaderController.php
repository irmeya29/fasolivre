<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;

class ReaderController extends Controller
{
    /**
     * Affiche le lecteur PDF du livre.
     */
    public function read($slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        // Vérifier si le livre est déjà dans la bibliothèque
        $userBook = $user->books()->where('book_id', $book->id)->first();

        // Si pas dans la bibliothèque :
        if (!$userBook) {
            if ($book->access_type === 'free') {
                // ✅ On ajoute automatiquement les livres gratuits
                $user->books()->attach($book->id, [
                    'progress'    => 0,
                    'is_favorite' => false,
                ]);

                $userBook = $user->books()->where('book_id', $book->id)->first();
            } else {
                // ❌ Livre payant / abonnement → pas d’accès
                return redirect()
                    ->route('books.show', $book->slug)
                    ->with('error', "Vous devez d'abord acheter ou débloquer ce livre pour y accéder.");
            }
        }

        $progress = $userBook->pivot->progress ?? 0;

        return view('front.read.pdf', [
            'book'      => $book,
            'progress'  => $progress,
            'updateUrl' => route('progress.update', $book->id),
        ]);
    }
}
