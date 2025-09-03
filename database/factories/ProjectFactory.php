<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-2 months', '+2 weeks');
        $deadline = $this->faker->dateTimeBetween($start, '+6 months');

        return [
            'name' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'start_date' => $start->format('Y-m-d'),
            'deadline' => $deadline->format('Y-m-d'),
            'owner_id' => null, // set in seeders if needed
        ];
    }
}

