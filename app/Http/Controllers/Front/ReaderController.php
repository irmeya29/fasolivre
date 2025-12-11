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

        // 📌 Si le livre n’est PAS dans la bibliothèque
        if (!$userBook) {

            // 👉 Si livre GRATUIT → on l’ajoute automatiquement
            if ($book->access_type === 'free') {

                $user->books()->attach($book->id, [
                    'progress'    => 0,
                    'is_favorite' => false,
                ]);

                // On recharge la relation pivot
                $userBook = $user->books()->where('book_id', $book->id)->first();
            }

            // 👉 Livre PAYANT ou SOUS ABONNEMENT → accès refusé
            else {
                return redirect()
                    ->route('books.show', $book->slug)
                    ->with('error', "Vous devez d'abord acheter ou débloquer ce livre pour y accéder.");
            }
        }

        // 🔥 Progression
        $progress = $userBook->pivot->progress ?? 0;

        return view('front.read.pdf', [
            'book'      => $book,
            'progress'  => $progress,
            'updateUrl' => route('progress.update', $book->id),
        ]);
    }



    /**
     * Affiche le lecteur AUDIO du livre.
     */
    public function audio($slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        // Vérifier si le livre est déjà dans la bibliothèque
        $userBook = $user->books()->where('book_id', $book->id)->first();

        if (!$userBook) {
            if ($book->access_type === 'free') {

                // Ajouter automatiquement les gratuits
                $user->books()->attach($book->id, [
                    'progress'    => 0,
                    'is_favorite' => false,
                ]);

                $userBook = $user->books()->where('book_id', $book->id)->first();
            } else {

                return redirect()
                    ->route('books.show', $book->slug)
                    ->with('error', "Vous devez débloquer ce livre pour écouter l’audio.");
            }
        }

        $progress = $userBook->pivot->progress ?? 0;

        return view('front.read.audio', [
            'book'      => $book,
            'progress'  => $progress,
            'updateUrl' => route('progress.update', $book->id),
        ]);
    }
}
