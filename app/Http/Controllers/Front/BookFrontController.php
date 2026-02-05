<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\BookPurchase;
use App\Models\Payment;
use App\Models\Subscription;

class BookFrontController extends Controller
{
    public function index()
    {
        $q        = request('q');
        $access   = request('access');   // free | paid | subscription
        $category = request('category'); // slug
        $sort     = request('sort', 'new'); // new | old | price_asc | price_desc

        $booksQuery = Book::with(['author', 'category'])
            ->where('status', 'published');

        if (!empty($q)) {
            $booksQuery->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhereHas('author', function ($a) use ($q) {
                        $a->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if (in_array($access, ['free', 'paid', 'subscription'], true)) {
            $booksQuery->where('access_type', $access);
        }

        if (!empty($category)) {
            $booksQuery->whereHas('category', function ($c) use ($category) {
                $c->where('slug', $category);
            });
        }

        switch ($sort) {
            case 'old':
                $booksQuery->orderByRaw("COALESCE(published_at, created_at) ASC");
                break;
            case 'price_asc':
                $booksQuery->orderByRaw("CASE WHEN price IS NULL THEN 1 ELSE 0 END, price ASC");
                break;
            case 'price_desc':
                $booksQuery->orderByRaw("CASE WHEN price IS NULL THEN 1 ELSE 0 END, price DESC");
                break;
            case 'new':
            default:
                $booksQuery->orderByRaw("COALESCE(published_at, created_at) DESC");
                break;
        }

        $books = $booksQuery->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        return view('front.books.index', compact('books', 'categories'));
    }

    public function show($slug)
    {
        $book = Book::with(['author', 'category'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $related = Book::with(['author', 'category'])
            ->where('status', 'published')
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->latest()
            ->take(10)
            ->get();

        $user = auth()->user();

        // defaults
        $purchase = null;
        $hasPurchase = false;
        $pendingPayment = null;
        $failedPayment = null;
        $hasActiveSub = false;

        if ($user) {
            $purchase = BookPurchase::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();

            $hasPurchase = $purchase && !is_null($purchase->purchased_at);

            if ($purchase) {
                $pendingPayment = Payment::where('payable_type', BookPurchase::class)
                    ->where('payable_id', $purchase->id)
                    ->where('status', 'PENDING')
                    ->latest('id')
                    ->first();

                $failedPayment = Payment::where('payable_type', BookPurchase::class)
                    ->where('payable_id', $purchase->id)
                    ->where('status', 'FAILED')
                    ->latest('id')
                    ->first();
            }

            $hasActiveSub = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '>', now())
                ->exists();
        }

        $isFree = $book->access_type === 'free';
        $isPaid = $book->access_type === 'paid';
        $isSub  = $book->access_type === 'subscription';

        $canRead =
            $isFree
            || ($isPaid && $hasPurchase)
            || ($isSub && $hasActiveSub)
            || $hasPurchase;

        // login redirect
        $loginUrl = $isPaid
            ? route('login', ['redirect' => url()->current(), 'autopay' => 1])
            : route('login', ['redirect' => url()->current()]);

        // auto pay only if paid + logged in + not purchased + no pending
        $shouldAutoPay = request()->boolean('autopay')
            && auth()->check()
            && $isPaid
            && !$hasPurchase
            && !$pendingPayment;

        return view('front.books.show', compact(
            'book', 'related',
            'purchase', 'hasPurchase', 'pendingPayment', 'failedPayment', 'hasActiveSub',
            'isFree', 'isPaid', 'isSub', 'canRead', 'loginUrl', 'shouldAutoPay'
        ));
    }
}
