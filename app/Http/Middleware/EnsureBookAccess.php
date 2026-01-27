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
        $slug = $request->route('slug');

        $book = Book::where('slug', $slug)->firstOrFail();

        // Ton model utilise access_type
        // Valeurs actuelles: free | paid
        // On ajoute: subscription (pour les livres accessibles via abonnement)
        $type = $book->access_type;

        // 1) Gratuit => OK
        if ($type === 'free') {
            return $next($request);
        }

        // 2) Achat individuel (paid) => doit avoir acheté
        $hasPurchase = BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNotNull('purchased_at')
            ->exists();

        if ($type === 'paid' && $hasPurchase) {
            return $next($request);
        }

        // 3) Abonnement => si abonnement actif, accès à tous les livres "subscription"
        $hasActiveSub = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();

        if ($type === 'subscription' && $hasActiveSub) {
            return $next($request);
        }

        // Optionnel (pratique) :
        // si tu autorises aussi l'achat même quand le livre est "subscription"
        if ($hasPurchase) {
            return $next($request);
        }

        return redirect()->route('account.index')
            ->with('error', "Accès refusé : livre payant ou abonnement requis.");
    }
}
