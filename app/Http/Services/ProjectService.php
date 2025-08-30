<?php
namespace App\Http\Services;
use App\Http\Services\ModelService;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class ProjectService extends ModelService
{
    public Model|Project $model;

    public array $data = [
        'name' => null,
        'description' => null,
    ];

    public function updateOrCreate(): Project|Model
    {
        $this->model->updateOrCreate(['id' => $this->model->id],[
            'name' => data_get($this->data, 'name'),
            'description' => data_get($this->data, 'description'),
        ]);

        return $this->model;
    }
}
