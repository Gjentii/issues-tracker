<?php

namespace App\Http\Controllers;

use App\Http\Requests\IssueRequest;
use App\Models\Issue;
use App\Models\User;
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

        $issue->members()->sync($request->input('members', []));

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

        if ($request->has('members')) {
            $issue->members()->sync($request->input('members', []));
        }

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

    public function members(Request $request, Issue $issue): JsonResponse
    {
        $members = $issue->members()->select('users.id', 'users.name', 'users.email')->get();
        return response()->json([
            'data' => $members,
        ]);
    }

    public function addMember(Request $request, Issue $issue): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        $userId = (int) $validated['user_id'];
        // attach ignoring duplicates
        $issue->members()->syncWithoutDetaching([$userId]);
        $user = User::select('id','name','email')->find($userId);
        return response()->json([
            'message' => 'Member added.',
            'member' => $user,
        ], 201);
    }

    public function removeMember(Request $request, Issue $issue, User $user): JsonResponse
    {
        $issue->members()->detach($user->id);
        return response()->json([
            'message' => 'Member detached.',
            'user_id' => $user->id,
        ]);
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
