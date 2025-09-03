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
            ->with('user:id,name')
            ->paginate(3, ['id','content','created_at','user_id']);

        $items = $comments->getCollection()->map(function ($c) {
            return [
                'id' => $c->id,
                'content' => $c->content,
                'created_at' => optional($c->created_at)->toIso8601String(),
                'author_name' => $c->user?->name,
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
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        return response()->json([
            'id' => $comment->id,
            'content' => $comment->content,
            'created_at' => $comment->created_at?->toIso8601String(),
            'author_name' => $request->user()->name,
        ], 201);
    }
}
