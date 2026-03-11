<?php

namespace Tests\Unit;

use App\Models\Task;
use PHPUnit\Framework\TestCase;

class TaskModelTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $task = new Task();

        $this->assertEquals(['name', 'priority', 'project_id'], $task->getFillable());
    }
}
