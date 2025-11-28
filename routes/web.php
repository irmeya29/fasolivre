<?php

use Illuminate\Support\Facades\Route;

// === FRONT CONTROLLERS ===
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Front\BookFrontController;
use App\Http\Controllers\Front\CategoryFrontController;
use App\Http\Controllers\Front\AuthorFrontController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\ReaderController;

// === ADMIN CONTROLLERS ===
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\DashboardController;


/*
|--------------------------------------------------------------------------
| FRONTEND PAGES (PUBLIC)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('front.pages.home');
})->name('home');


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
| SEARCH (Public)
|--------------------------------------------------------------------------
*/
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/ajax', [SearchController::class, 'ajax'])->name('search.ajax');


/*
|--------------------------------------------------------------------------
| PDF READER (Public — accès contrôlé en controller)
|--------------------------------------------------------------------------
*/
Route::get('/read/{slug}', [ReaderController::class, 'read'])->name('read.book');

// FRONT — SUBMISSION
Route::middleware('auth')->group(function () {
    Route::get('/submit', [\App\Http\Controllers\Front\SubmissionFrontController::class, 'create'])->name('submit.create');
    Route::post('/submit', [\App\Http\Controllers\Front\SubmissionFrontController::class, 'store'])->name('submit.store');
});



/*
|--------------------------------------------------------------------------
| USER ACCOUNT (Private)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {

    Route::get('/', function () {
        return view('front.account.index');
    })->name('index');

    Route::get('/books', function () {
        return view('front.account.books');
    })->name('books');

    Route::get('/settings', function () {
        return view('front.account.settings');
    })->name('settings');

});


/*
|--------------------------------------------------------------------------
| PROFILE (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 👉 On désactive l'ancien tableau de bord Breeze
    // Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // === LOGIN ADMIN ===
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // === LOGOUT ADMIN ===
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    // === ADMIN PROTECTED AREA ===
    Route::middleware('auth:admin')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->names('categories');
        Route::resource('authors', AuthorController::class)->names('authors');
        Route::resource('books', BookController::class)->names('books');
        Route::resource('submissions', SubmissionController::class)->names('submissions');
    });

    // DEFAULT REDIRECT
    Route::get('/', fn() => redirect()->route('admin.dashboard'));

});


/*
|--------------------------------------------------------------------------
| AUTH ROUTES (BREEZE)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
