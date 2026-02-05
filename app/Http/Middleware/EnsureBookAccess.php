<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Subscription;

class EnsureBookAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $slug = (string) $request->route('slug');

        // sécurité
        if (!$user) {
            return redirect()->route('login', ['redirect' => url()->current()]);
        }

        $book = Book::where('slug', $slug)->firstOrFail();

        // 1) Gratuit => OK
        if ($book->access_type === 'free') {
            return $next($request);
        }

        // Achat individuel (validé uniquement si purchased_at non null)
        $hasPurchase = BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNotNull('purchased_at')
            ->exists();

        // 2) Livre payant => achat requis
        if ($book->access_type === 'paid') {
            if ($hasPurchase) return $next($request);

            // ✅ au lieu d'aller sur account, on renvoie vers la page du livre
            return redirect()->route('books.show', $book->slug)
                ->with('error', "Ce livre nécessite un achat.");
        }

        // 3) Abonnement => abonnement actif OU achat individuel (si tu autorises)
        if ($book->access_type === 'subscription') {
            $hasActiveSub = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '>', now())
                ->exists();

            if ($hasActiveSub || $hasPurchase) return $next($request);

            return redirect()->route('books.show', $book->slug)
                ->with('error', "Ce livre nécessite un abonnement.");
        }

        // Valeur inconnue
        return redirect()->route('books.show', $book->slug)
            ->with('error', "Accès non autorisé.");
    }
}
