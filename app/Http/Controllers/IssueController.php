<?php

namespace App\Http\Controllers;

use App\Http\Requests\IssueRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function store(IssueRequest $request): RedirectResponse|JsonResponse
    {
        $issue = Issue::create($request->validated());
        // Attach selected tags (attach none if not provided)
        $issue->tags()->sync($request->input('tags', []));

        if ($request->expectsJson()) {
            $html = view('components.issue-card', [
                'issue' => $issue->fresh(),
                'viewUrl' => route('issues.show', $issue),
                'editUrl' => route('issues.edit', $issue),
                'deleteUrl' => route('issues.destroy', $issue),
            ])->render();

            return response()->json([
                'message' => 'Issue created successfully.',
                'issue' => $issue->toArray(),
                'html' => $html,
            ], 201);
        }

        return redirect()->back();
    }

    public function update(IssueRequest $request, Issue $issue): RedirectResponse|JsonResponse
    {
        $issue->update($request->validated());
        // Sync tags to reflect current selection (attach/detach)
        $issue->tags()->sync($request->input('tags', []));

        if ($request->expectsJson()) {
            $html = view('components.issue-card', [
                'issue' => $issue->fresh(),
                'viewUrl' => route('issues.show', $issue),
                'editUrl' => route('issues.edit', $issue),
                'deleteUrl' => route('issues.destroy', $issue),
            ])->render();

            return response()->json([
                'message' => 'Issue updated successfully.',
                'issue' => $issue->toArray(),
                'html' => $html,
            ]);
        }

        return redirect()->back();
    }

    public function destroy(Request $request, Issue $issue): RedirectResponse|JsonResponse
    {
        $issue->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Issue deleted successfully.',
                'id' => $issue->id,
            ]);
        }

        return redirect()->back();
    }
}
