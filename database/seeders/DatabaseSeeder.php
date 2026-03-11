<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::factory(3)->create();

        $projects->each(function (Project $project) {
            Task::factory(5)->sequence(
                fn ($sequence) => ['priority' => $sequence->index + 1],
            )->for($project)->create();
        });
    }
}