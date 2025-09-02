<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Services\ProjectService;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function saveData(ProjectRequest $request, Project $project): Project
    {
        return (new ProjectService())
            ->setData($request->all())
            ->setModel($project)
            ->updateOrCreate();
    }

    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function show(Project $project, Request $request)
    {
        $issuesQuery = $project->issues()->with('tags')->latest();

        // Search by title or description
        if ($search = trim((string) $request->input('q'))) {
            $issuesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $issuesQuery->where('status', $status);
        }

        // Filter by priority
        if ($priority = $request->input('priority')) {
            $issuesQuery->where('priority', $priority);
        }

        // Filter by tags (any of selected tags)
        $tagIds = array_filter((array) $request->input('tags', []));
        if (!empty($tagIds)) {
            $issuesQuery->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        $issues = $issuesQuery->get();

        $tags = Tag::query()->orderBy('name')->get();
        $statuses = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'closed' => 'Closed',
        ];
        $priorities = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
        ];

        return view('projects.show', compact('project', 'issues', 'tags', 'statuses', 'priorities'));
    }

    public function create()
    {
        return view('projects.edit-create');
    }

    public function store(ProjectRequest $request, Project $project)
    {
        $this->saveData($request, $project);

        return redirect()->route('projects.index');

    }
    public function edit(Project $project)
    {
        return view('projects.edit-create', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->saveData($request, $project);
        return redirect()->route('projects.index');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }

    public function issues(Project $project, Request $request)
    {
        $issuesQuery = $project->issues()->with('tags')->latest();

        if ($search = trim((string) $request->input('q'))) {
            $issuesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $issuesQuery->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $issuesQuery->where('priority', $priority);
        }

        $tagIds = array_filter((array) $request->input('tags', []));
        if (!empty($tagIds)) {
            $issuesQuery->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        $issues = $issuesQuery->get();

        $html = view('projects.partials.issues-grid', [
            'issues' => $issues,
        ])->render();

        return response()->json([
            'html' => $html,
            'count' => $issues->count(),
        ]);
    }
}
