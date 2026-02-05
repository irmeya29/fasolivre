<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class ReaderController extends Controller
{
    public function read($slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // sécurité
        if (!$user) {
            return redirect()->route('login', ['redirect' => url()->current()]);
        }

        // ✅ Vérifie droit d'accès (FREE / achat / abonnement)
        $hasPurchase = BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNotNull('purchased_at')
            ->exists();

        $hasActiveSub = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();

        $canAccess = $book->access_type === 'free'
            || ($book->access_type === 'paid' && $hasPurchase)
            || ($book->access_type === 'subscription' && ($hasActiveSub || $hasPurchase));

        if (!$canAccess) {
            return redirect()->route('books.show', $book->slug)
                ->with('error', "Vous devez d'abord acheter ou débloquer ce livre pour y accéder.");
        }

        // ✅ Assure la présence dans la bibliothèque (pivot book_user)
        $userBook = $user->books()->where('book_id', $book->id)->first();
        if (!$userBook) {
            $user->books()->attach($book->id, [
                'progress'    => 0,
                'is_favorite' => false,
            ]);
            $userBook = $user->books()->where('book_id', $book->id)->first();
        }

        $progress = $userBook->pivot->progress ?? 0;

        return view('front.read.pdf', [
            'book'      => $book,
            'progress'  => $progress,
            'updateUrl' => route('progress.update', $book->id),
        ]);
    }

    public function audio($slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login', ['redirect' => url()->current()]);
        }

        $hasPurchase = BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNotNull('purchased_at')
            ->exists();

        $hasActiveSub = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();

        $canAccess = $book->access_type === 'free'
            || ($book->access_type === 'paid' && $hasPurchase)
            || ($book->access_type === 'subscription' && ($hasActiveSub || $hasPurchase));

        if (!$canAccess) {
            return redirect()->route('books.show', $book->slug)
                ->with('error', "Vous devez débloquer ce livre pour écouter l’audio.");
        }

        $userBook = $user->books()->where('book_id', $book->id)->first();
        if (!$userBook) {
            $user->books()->attach($book->id, [
                'progress'    => 0,
                'is_favorite' => false,
            ]);
            $userBook = $user->books()->where('book_id', $book->id)->first();
        }

        $progress = $userBook->pivot->progress ?? 0;

        return view('front.read.audio', [
            'book'      => $book,
            'progress'  => $progress,
            'updateUrl' => route('progress.update', $book->id),
        ]);
    }
}
