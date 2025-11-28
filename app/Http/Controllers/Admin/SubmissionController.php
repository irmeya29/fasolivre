<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.submissions.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        return view('admin.submissions.show', compact('submission'));
    }

    public function edit(Submission $submission)
    {
        return view('admin.submissions.edit', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $submission->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.submissions.index')
            ->with('success', 'Statut mis à jour.');
    }

    public function destroy(Submission $submission)
    {
        if ($submission->pdf && Storage::disk('public')->exists($submission->pdf)) {
            Storage::disk('public')->delete($submission->pdf);
        }

        $submission->delete();

        return redirect()
            ->route('admin.submissions.index')
            ->with('success', 'Soumission supprimée.');
    }
}
