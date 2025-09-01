<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::latest()->paginate(20);
        return view('tags.index', compact('tags'));
    }

    public function store(TagRequest $request): RedirectResponse|JsonResponse
    {
        $tag = Tag::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tag created successfully.',
                'tag' => $tag,
            ], 201);
        }

        return redirect()->route('tags.index');
    }

    public function validateAjax(Request $request): JsonResponse
    {
        // Use TagRequest rules for consistency
        $rules = (new TagRequest())->rules();

        // If a single field is provided, validate only that field
        if ($request->has('field')) {
            $field = $request->input('field');
            if (array_key_exists($field, $rules)) {
                $rules = [ $field => $rules[$field] ];
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json(['errors' => (object)[]]);
    }

    public function table(Request $request): View
    {
        $tags = Tag::orderBy('name')->paginate(20);
        return view('tags.table', compact('tags'));
    }
}
