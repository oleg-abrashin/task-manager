<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->optional()->dateTimeBetween('now', '+1 month');

        return [
            'name' => fake()->sentence(3),
            'priority' => 0,
            'project_id' => Project::factory(),
            'start_date' => $startDate,
            'due_date' => $startDate ? fake()->dateTimeBetween($startDate, '+3 months') : null,
        ];
    }
}
