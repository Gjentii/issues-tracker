<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(Issue $issue): JsonResponse
    {
        $comments = $issue->comments()
            ->latest()
            ->paginate(3, ['id','content','created_at']);

        $items = $comments->getCollection()->map(function ($c) {
            return [
                'id' => $c->id,
                'content' => $c->content,
                'created_at' => optional($c->created_at)->toIso8601String(),
            ];
        })->values();

        return response()->json([
            'data' => $items,
            'current_page' => $comments->currentPage(),
            'next_page_url' => $comments->nextPageUrl(),
            'last_page' => $comments->lastPage(),
            'per_page' => $comments->perPage(),
            'total' => $comments->total(),
        ]);
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
