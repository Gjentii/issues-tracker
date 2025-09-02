<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(Issue $issue): JsonResponse
    {
        $items = $issue->comments()
            ->latest()
            ->get(['id','content','created_at']);
        return response()->json($items);
    }

    public function store(Request $request, Issue $issue): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $comment = $issue->comments()->create([
            'content' => $data['content'],
        ]);

        return response()->json([
            'id' => $comment->id,
            'content' => $comment->content,
            'created_at' => $comment->created_at?->toIso8601String(),
        ], 201);
    }
}
