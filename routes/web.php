<?php

use Illuminate\Support\Facades\Route;

// FRONT CONTROLLERS
use App\Http\Controllers\Front\BookFrontController;
use App\Http\Controllers\Front\CategoryFrontController;
use App\Http\Controllers\Front\AuthorFrontController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\SubmissionFrontController;
use App\Http\Controllers\Front\ReaderController;

// USER FEATURES
use App\Http\Controllers\BookInteractionController;
use App\Http\Controllers\ProfileController;

// ADMIN CONTROLLERS
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\DashboardController;


/*
|--------------------------------------------------------------------------
| HOME (Public)
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('front.pages.home'))->name('home');


/*
|--------------------------------------------------------------------------
| BOOKS (Public)
|--------------------------------------------------------------------------
*/
Route::get('/books', [BookFrontController::class, 'index'])->name('books.index');
Route::get('/books/{slug}', [BookFrontController::class, 'show'])->name('books.show');


/*
|--------------------------------------------------------------------------
| CATEGORIES (Public)
|--------------------------------------------------------------------------
*/
Route::get('/categories', [CategoryFrontController::class, 'index'])->name('categories.index.front');
Route::get('/categories/{slug}', [CategoryFrontController::class, 'show'])->name('categories.show');


/*
|--------------------------------------------------------------------------
| AUTHORS (Public)
|--------------------------------------------------------------------------
*/
Route::get('/authors', [AuthorFrontController::class, 'index'])->name('authors.index.front');
Route::get('/authors/{slug}', [AuthorFrontController::class, 'show'])->name('authors.show');


/*
|--------------------------------------------------------------------------
| 🔍 SEARCH (Public)
|--------------------------------------------------------------------------
*/
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/ajax', [SearchController::class, 'ajax'])->name('search.ajax');


/*
|--------------------------------------------------------------------------
| 📖 LECTURE + PROGRESSION + FAVORIS (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Lecteur PDF / Audio
    Route::get('/read/{slug}', [ReaderController::class, 'read'])
        ->name('read.book');

    // Sauvegarder progression (PDF Reader auto-save)
    Route::post('/read/{book}/progress', [ReaderController::class, 'saveProgress'])
        ->name('read.saveProgress');

    // Favoris
    Route::post('/book/{book}/favorite', [BookInteractionController::class, 'toggleFavorite'])
        ->name('favorite.toggle');

    // Récupérer progression
    Route::get('/book/{book}/progress', [BookInteractionController::class, 'getProgress'])
        ->name('progress.get');
});


/*
|--------------------------------------------------------------------------
| ✍️ SUBMISSION — Manuscrits (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/submit', [SubmissionFrontController::class, 'create'])->name('submit.create');
    Route::post('/submit', [SubmissionFrontController::class, 'store'])->name('submit.store');
});


/*
|--------------------------------------------------------------------------
| 👤 USER ACCOUNT (Privé)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {

    Route::get('/', fn() => view('front.account.index'))->name('index');
    Route::get('/books', fn() => view('front.account.books'))->name('books');

    // ⚠️ settings supprimé → gestion dans /profile (Breeze)
});


/*
|--------------------------------------------------------------------------
| 🔐 PROFILE (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

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

    /* LOGIN ADMIN */
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    /* LOGOUT ADMIN */
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    /* ADMIN PROTECTED AREA */
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
| AUTH SCAFFOLDING (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
