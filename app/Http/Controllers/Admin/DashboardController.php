<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\Submission;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'books' => Book::count(),
            'authors' => Author::count(),
            'categories' => Category::count(),
            'pending_submissions' => Submission::where('status', 'pending')->count(),

            'latest_books' => Book::orderBy('id', 'desc')->limit(5)->get(),
            'latest_submissions' => Submission::with('user')->orderBy('id', 'desc')->limit(5)->get(),
        ]);
    }
}
