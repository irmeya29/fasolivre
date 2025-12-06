<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ReadingProgress;
use Illuminate\Http\Request;

class BookInteractionController extends Controller
{
    /**
     * -------------------------------------
     * FAVORIS : TOGGLE ON/OFF
     * -------------------------------------
     */
    public function toggleFavorite(Book $book)
    {
        $user = auth()->user();

        if ($user->favorites()->where('book_id', $book->id)->exists()) {
            $user->favorites()->detach($book->id);

            return response()->json([
                'status' => 'removed',
                'message' => 'Livre retiré des favoris.'
            ]);
        }

        $user->favorites()->attach($book->id);

        return response()->json([
            'status' => 'added',
            'message' => 'Livre ajouté aux favoris.'
        ]);
    }


    /**
     * -------------------------------------
     * SAUVEGARDE DE LA PROGRESSION
     * -------------------------------------
     */
    public function updateProgress(Request $req, Book $book)
    {
        $req->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $progress = ReadingProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'book_id' => $book->id
            ],
            [
                'progress' => $req->progress
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $progress->progress,
            'message' => 'Progression mise à jour.'
        ]);
    }


    /**
     * -------------------------------------
     * RÉCUPÉRER LA PROGRESSION D'UN LIVRE
     * -------------------------------------
     */
    public function getProgress(Book $book)
    {
        $progress = ReadingProgress::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->value('progress') ?? 0;

        return response()->json([
            'progress' => $progress
        ]);
    }
}
