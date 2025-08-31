<?php

namespace App\Http\Services;

use Illuminate\Database\Eloquent\Model;

abstract class ModelService
{
    public array $data = [];

    public Model $model;

    public static function make(): static
    {
        return new static(...func_get_args());
    }

    abstract public function updateOrCreate(): Model;

    public function setData(array $data = []): static
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function setModel(Model $model): static
    {
        $this->model = $model;
        return $this;
    }
}
