<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->pluck('id')->all();

        $projects = [
            'Website Redesign',
            'API Platform',
            'Admin Dashboard',
            'Mobile Companion App',
            'Analytics & Reporting',
            'Marketing Landing Pages',
            'Design System',
            'Developer Portal',
        ];

        foreach ($projects as $name) {
            $start = Carbon::now()->subDays(rand(0, 60));
            $deadline = (clone $start)->addDays(rand(15, 120));

            Project::firstOrCreate(
                ['name' => $name],
                [
                    'description' => 'Seeded project: ' . Str::lower($name) . ' initiative.',
                    'start_date' => $start->toDateString(),
                    'deadline' => $deadline->toDateString(),
                    'owner_id' => !empty($users) ? $users[array_rand($users)] : null,
                ]
            );
        }
    }
}

