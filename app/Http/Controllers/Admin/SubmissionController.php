<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::query()
            ->with(['user', 'category'])
            ->latest();

        // ✅ filtres optionnels
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('full_name', 'like', "%{$q}%")
                    ->orWhere('phone_number', 'like', "%{$q}%");
            });
        }

        $submissions = $query->paginate(12)->withQueryString();

        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.submissions.index', compact('submissions', 'categories'));
    }

    public function show(Submission $submission)
    {
        $submission->load(['user', 'category']);

        return view('admin.submissions.show', compact('submission'));
    }

    public function edit(Submission $submission)
    {
        $submission->load(['user', 'category']);

        return view('admin.submissions.edit', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,accepted,rejected'],
        ]);

        $submission->update([
            'status' => $validated['status'],
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

    // ✅ OPTIONNEL (mieux que asset(storage/..)) : download sécurisé
    public function download(Submission $submission)
    {
        abort_unless($submission->pdf && Storage::disk('public')->exists($submission->pdf), 404);

        // nom de fichier propre
        $name = 'manuscrit-' . $submission->id . '.pdf';

        return Storage::disk('public')->download($submission->pdf, $name);
    }
}
