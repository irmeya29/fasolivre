<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionFrontController extends Controller
{
    public function create()
    {
        return view('front.submit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'pdf'         => 'required|mimes:pdf|max:20000',
        ]);

        $path = $request->file('pdf')->store('submissions', 'public');

        Submission::create([
            'user_id'    => Auth::id(),
            'title'      => $request->title,
            'description'=> $request->description,
            'pdf'        => $path,
            'status'     => 'pending',
        ]);

        return redirect()->route('submit.create')
            ->with('success', 'Votre manuscrit a été envoyé avec succès.');
    }
}
