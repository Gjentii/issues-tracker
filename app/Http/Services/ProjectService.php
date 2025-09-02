<?php
namespace App\Http\Services;
use App\Http\Services\ModelService;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ProjectService extends ModelService
{
    public Model|Project $model;

    public array $data = [
        'name' => null,
        'description' => null,
        'start_date' => null,
        'deadline' => null,
    ];

    public function updateOrCreate(): Project|Model
    {
        $isNew = !$this->model->exists || empty($this->model->id);
        $this->model->updateOrCreate(['id' => $this->model->id],[
            'name' => data_get($this->data, 'name'),
            'description' => data_get($this->data, 'description'),
            'start_date' => $this->parseDate(data_get($this->data, 'start_date')),
            'deadline' => $this->parseDate(data_get($this->data, 'deadline')),
            'owner_id' => $isNew ? (auth()->id() ?? null) : ($this->model->owner_id ?? null),
        ]);

        return $this->model;
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) return null;
        try {
            return Carbon::createFromFormat('d-m-Y', (string)$value)->format('Y-m-d');
        } catch (\Throwable $e) {
            try { return Carbon::parse((string)$value)->format('Y-m-d'); } catch (\Throwable $e2) { return null; }
        }
    }
}
