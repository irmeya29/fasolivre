<?php

namespace App\Http\Middleware;

use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Middleware est utilisé avec 'auth', donc user doit exister
        if (!$user) {
            abort(403, 'Connexion requise.');
        }

        // On récupère le livre par slug (route: /read/{slug} et /listen/{slug})
        $slug = $request->route('slug');
        if (!$slug) {
            abort(404, 'Livre introuvable.');
        }

        $book = Book::where('slug', $slug)->first();
        if (!$book) {
            abort(404, 'Livre introuvable.');
        }

        // 1) Gratuit => OK
        if ($book->access_type === 'free') {
            return $next($request);
        }

        // Achat (si payé)
        $hasPurchase = BookPurchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNotNull('purchased_at')
            ->exists();

        // 2) Payant => achat requis
        if ($book->access_type === 'paid') {
            abort_unless($hasPurchase, 403, 'Ce livre nécessite un achat.');
            return $next($request);
        }

        // 3) Abonnement => abonnement actif requis (ou achat si tu autorises aussi)
        if ($book->access_type === 'subscription') {
            $hasActiveSub = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '>', now())
                ->exists();

            // ✅ si tu veux autoriser l'achat individuel même en subscription, on accepte aussi $hasPurchase
            abort_unless($hasActiveSub || $hasPurchase, 403, 'Ce livre nécessite un abonnement.');
            return $next($request);
        }

        // Valeur access_type inconnue => on bloque
        abort(403, 'Accès non autorisé.');
    }
}
