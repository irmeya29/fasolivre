<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionFrontController extends Controller
{
    public function create()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('front.submit', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'pdf'         => ['required', 'file', 'mimes:pdf', 'max:20000'],

            'category_id' => ['required', 'integer', 'exists:categories,id'],

            // Contact
            'full_name'          => ['nullable', 'string', 'max:120'],
            'phone_country_code' => ['required', 'string', 'max:10'],  // ex: +226
            'phone_number'       => ['required', 'string', 'max:30'],  // ex: 70123456

            // Adresse
            'address_line' => ['required', 'string', 'max:255'],
            'city'         => ['required', 'string', 'max:120'],
            'country'      => ['required', 'string', 'max:120'],
        ]);

        $path = $request->file('pdf')->store('submissions', 'public');

        Submission::create([
            'user_id'     => Auth::id(),
            'category_id' => $validated['category_id'],
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'pdf'         => $path,
            'status'      => 'pending',

            'full_name'          => $validated['full_name'] ?? null,
            'phone_country_code' => $validated['phone_country_code'],
            'phone_number'       => $validated['phone_number'],
            'address_line'       => $validated['address_line'],
            'city'               => $validated['city'],
            'country'            => $validated['country'],
        ]);

        return redirect()
            ->route('submit.create')
            ->with('success', 'Votre manuscrit a été envoyé avec succès.');
    }
}
