<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'HTML',         'color' => '#E34F26'],
            ['name' => 'CSS',          'color' => '#264DE4'],
            ['name' => 'JavaScript',   'color' => '#F7DF1E'],
            ['name' => 'TypeScript',   'color' => '#3178C6'],
            ['name' => 'PHP',          'color' => '#777BB3'],
            ['name' => 'Laravel',      'color' => '#FF2D20'],
            ['name' => 'Node.js',      'color' => '#3C873A'],
            ['name' => 'React',        'color' => '#61DAFB'],
            ['name' => 'Vue',          'color' => '#42B883'],
            ['name' => 'Tailwind CSS', 'color' => '#38BDF8'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name']],
                ['color' => $tag['color']]
            );
        }
    }
}

