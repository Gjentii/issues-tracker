<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Services\ProjectService;
use App\Models\Project;

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

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(ProjectRequest $request, Project $project)
    {
        $this->saveData($request, $project);

        return redirect()->route('projects.index');

    }
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
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
}
