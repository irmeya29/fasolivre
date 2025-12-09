<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookInteractionController extends Controller
{
    /**
     * Toggle favoris ON / OFF
     */
    public function toggleFavorite(Book $book)
    {
        $user = auth()->user();

        // Vérifier que le livre est dans la bibliothèque
        if (!$user->books->contains($book->id)) {
            $user->books()->attach($book->id, [
                'progress'    => 0,
                'is_favorite' => true,
            ]);
        } else {
            $current = $user->books()->where('book_id', $book->id)->first();
            $newValue = !$current->pivot->is_favorite;

            $user->books()->updateExistingPivot($book->id, [
                'is_favorite' => $newValue,
            ]);
        }

        return response()->json([
            'success' => true,
            'favorite' => $user->books()->where('book_id', $book->id)->first()->pivot->is_favorite,
        ]);
    }

    /**
     * Mettre à jour la progression (0–100 %)
     */
    public function updateProgress(Request $request, Book $book)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $user = auth()->user();

        // s'assurer que le livre est dans la bibliothèque
        if (!$user->books->contains($book->id)) {
            $user->books()->attach($book->id, [
                'progress'    => $request->progress,
                'is_favorite' => false,
            ]);
        } else {
            $user->books()->updateExistingPivot($book->id, [
                'progress' => $request->progress,
            ]);
        }

        return response()->json([
            'success'  => true,
            'progress' => $request->progress,
        ]);
    }

    /**
     * Récupérer la progression (pour init le lecteur)
     */
    public function getProgress(Book $book)
    {
        $user = auth()->user();

        $pivot = $user->books()->where('book_id', $book->id)->first()?->pivot;

        return response()->json([
            'progress' => $pivot?->progress ?? 0,
        ]);
    }
}
