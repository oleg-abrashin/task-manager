<?php

namespace Tests\Unit;

use App\Models\Task;
use PHPUnit\Framework\TestCase;

class TaskModelTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $task = new Task();

        $this->assertEquals(
            ['name', 'priority', 'project_id', 'start_date', 'due_date'],
            $task->getFillable()
        );
    }

    public function test_casts_include_dates(): void
    {
        $task = new Task();
        $casts = $task->getCasts();

        $this->assertEquals('date', $casts['start_date']);
        $this->assertEquals('date', $casts['due_date']);
    }
}
