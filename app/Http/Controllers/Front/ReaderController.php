<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    /**
     * Affiche le lecteur PDF du livre.
     */
    public function read($slug)
    {
        // Charger le livre
        $book = Book::where('slug', $slug)->firstOrFail();

        // Vérifier que l’utilisateur a accès à ce livre
        if (!auth()->user()->books->contains($book->id)) {
            return redirect()->route('books.show', $book->slug)
                ->with('error', "Vous n’avez pas accès à ce livre.");
        }

        // Récupérer la relation pivot (progression)
        $userBook = auth()->user()->books()
            ->where('book_id', $book->id)
            ->first();

        // Si pour une raison quelconque le pivot n’existe pas encore → on le crée
        if (!$userBook) {
            auth()->user()->books()->attach($book->id, [
                'progress' => 0,
                'is_favorite' => 0,
            ]);

            $progress = 0;
        } else {
            $progress = $userBook->pivot->progress ?? 0;
        }

        // Retourne la vue du lecteur PDF
        return view('front.read.pdf', [
            'book'      => $book,
            'progress'  => $progress,
            'updateUrl' => route('progress.update', $book->id),
        ]);
    }
}
