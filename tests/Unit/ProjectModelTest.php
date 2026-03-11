<?php

namespace Tests\Unit;

use App\Models\Project;
use PHPUnit\Framework\TestCase;

class ProjectModelTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $project = new Project();

        $this->assertEquals(['name'], $project->getFillable());
    }
}
