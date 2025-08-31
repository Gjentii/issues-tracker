<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /* @var Project $project */

        $project = $this->resource;

        $data = [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
        ];

        return $data;
    }
}
