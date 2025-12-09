<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Front\BookFrontController;
use App\Http\Controllers\Front\CategoryFrontController;
use App\Http\Controllers\Front\AuthorFrontController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\SubmissionFrontController;
use App\Http\Controllers\Front\ReaderController;
use App\Http\Controllers\BookInteractionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\DashboardController;

/* HOME */
Route::get('/', fn() => view('front.pages.home'))->name('home');

/* BOOKS */
Route::get('/books', [BookFrontController::class, 'index'])->name('books.index');
Route::get('/books/{slug}', [BookFrontController::class, 'show'])->name('books.show');

/* CATEGORIES */
Route::get('/categories', [CategoryFrontController::class, 'index'])->name('categories.index.front');
Route::get('/categories/{slug}', [CategoryFrontController::class, 'show'])->name('categories.show');

/* AUTHORS */
Route::get('/authors', [AuthorFrontController::class, 'index'])->name('authors.index.front');
Route::get('/authors/{slug}', [AuthorFrontController::class, 'show'])->name('authors.show');

/* SEARCH */
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/ajax', [SearchController::class, 'ajax'])->name('search.ajax');

/* LECTURE + PROGRESSION + FAVORIS (auth) */
Route::middleware('auth')->group(function () {

    Route::get('/read/{slug}', [ReaderController::class, 'read'])
        ->name('read.book');

    Route::post('/book/{book}/progress', [BookInteractionController::class, 'updateProgress'])
        ->name('progress.update');

    Route::get('/book/{book}/progress', [BookInteractionController::class, 'getProgress'])
        ->name('progress.get');

    Route::post('/book/{book}/favorite', [BookInteractionController::class, 'toggleFavorite'])
        ->name('favorite.toggle');
});

/* SUBMISSION (auth) */
Route::middleware('auth')->group(function () {
    Route::get('/submit', [SubmissionFrontController::class, 'create'])->name('submit.create');
    Route::post('/submit', [SubmissionFrontController::class, 'store'])->name('submit.store');
});

/* ACCOUNT (auth) */
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', fn() => view('front.account.index'))->name('index');
    Route::get('/books', fn() => view('front.account.books'))->name('books');
});

/* PROFILE (Breeze) */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 🛠️ ADMIN PANEL
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // LOGIN ADMIN
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // LOGOUT
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    // ZONE ADMIN PROTÉGÉE
    Route::middleware('auth:admin')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('authors', AuthorController::class);
        Route::resource('books', BookController::class);
        Route::resource('submissions', SubmissionController::class);
    });

    Route::get('/', fn() => redirect()->route('admin.dashboard'));
});


/*
|--------------------------------------------------------------------------
| AUTH (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
