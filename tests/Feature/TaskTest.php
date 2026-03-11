<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->project = Project::factory()->create();
    }

    public function test_task_index_displays_tasks(): void
    {
        $task = Task::factory()->for($this->project)->create(['priority' => 1]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee($task->name);
    }

    public function test_task_index_filters_by_project(): void
    {
        $otherProject = Project::factory()->create();
        $task = Task::factory()->for($this->project)->create(['priority' => 1]);
        $otherTask = Task::factory()->for($otherProject)->create(['priority' => 1]);

        $response = $this->get(route('tasks.index', ['project_id' => $this->project->id]));

        $response->assertOk();
        $response->assertSee($task->name);
        $response->assertDontSee($otherTask->name);
    }

    public function test_task_create_form_displays(): void
    {
        $response = $this->get(route('tasks.create'));

        $response->assertOk();
        $response->assertSee('Create Task');
        $response->assertSee($this->project->name);
    }

    public function test_task_can_be_stored(): void
    {
        $response = $this->post(route('tasks.store'), [
            'name' => 'Test Task',
            'project_id' => $this->project->id,
            'priority' => 5,
            'start_date' => '2026-04-01',
            'due_date' => '2026-04-15',
        ]);

        $response->assertRedirect();

        $task = Task::where('name', 'Test Task')->first();
        $this->assertNotNull($task);
        $this->assertEquals($this->project->id, $task->project_id);
        $this->assertEquals(5, $task->priority);
        $this->assertEquals('2026-04-01', $task->start_date->format('Y-m-d'));
        $this->assertEquals('2026-04-15', $task->due_date->format('Y-m-d'));
    }

    public function test_task_store_auto_assigns_priority_when_empty(): void
    {
        Task::factory()->for($this->project)->create(['priority' => 1]);
        Task::factory()->for($this->project)->create(['priority' => 2]);

        $this->post(route('tasks.store'), [
            'name' => 'Third Task',
            'project_id' => $this->project->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Third Task',
            'priority' => 3,
        ]);
    }

    public function test_task_store_validates_required_fields(): void
    {
        $response = $this->post(route('tasks.store'), []);

        $response->assertSessionHasErrors(['name', 'project_id']);
    }

    public function test_task_store_validates_due_date_after_start_date(): void
    {
        $response = $this->post(route('tasks.store'), [
            'name' => 'Task',
            'project_id' => $this->project->id,
            'start_date' => '2026-05-10',
            'due_date' => '2026-05-01',
        ]);

        $response->assertSessionHasErrors('due_date');
    }

    public function test_task_edit_form_displays(): void
    {
        $task = Task::factory()->for($this->project)->create([
            'priority' => 1,
            'start_date' => '2026-04-01',
            'due_date' => '2026-04-15',
        ]);

        $response = $this->get(route('tasks.edit', $task));

        $response->assertOk();
        $response->assertSee('Edit Task');
        $response->assertSee($task->name);
        $response->assertSee('2026-04-01');
        $response->assertSee('2026-04-15');
    }

    public function test_task_can_be_updated(): void
    {
        $task = Task::factory()->for($this->project)->create(['priority' => 1]);

        $response = $this->put(route('tasks.update', $task), [
            'name' => 'Updated Name',
            'project_id' => $this->project->id,
            'priority' => 3,
            'start_date' => '2026-06-01',
            'due_date' => '2026-06-30',
        ]);

        $response->assertRedirect();

        $task->refresh();
        $this->assertEquals('Updated Name', $task->name);
        $this->assertEquals(3, $task->priority);
        $this->assertEquals('2026-06-01', $task->start_date->format('Y-m-d'));
        $this->assertEquals('2026-06-30', $task->due_date->format('Y-m-d'));
    }

    public function test_task_can_be_deleted(): void
    {
        $task = Task::factory()->for($this->project)->create(['priority' => 1]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_delete_reorders_remaining_tasks(): void
    {
        $task1 = Task::factory()->for($this->project)->create(['priority' => 1]);
        $task2 = Task::factory()->for($this->project)->create(['priority' => 2]);
        $task3 = Task::factory()->for($this->project)->create(['priority' => 3]);

        $this->delete(route('tasks.destroy', $task1));

        $this->assertEquals(1, $task2->fresh()->priority);
        $this->assertEquals(2, $task3->fresh()->priority);
    }

    public function test_tasks_can_be_reordered(): void
    {
        $task1 = Task::factory()->for($this->project)->create(['priority' => 1]);
        $task2 = Task::factory()->for($this->project)->create(['priority' => 2]);
        $task3 = Task::factory()->for($this->project)->create(['priority' => 3]);

        $response = $this->postJson(route('tasks.reorder'), [
            'ids' => [$task3->id, $task1->id, $task2->id],
        ]);

        $response->assertOk();
        $this->assertEquals(1, $task3->fresh()->priority);
        $this->assertEquals(2, $task1->fresh()->priority);
        $this->assertEquals(3, $task2->fresh()->priority);
    }

    public function test_reorder_validates_ids(): void
    {
        $response = $this->postJson(route('tasks.reorder'), ['ids' => []]);

        $response->assertUnprocessable();
    }

    public function test_task_has_timestamps(): void
    {
        $task = Task::factory()->for($this->project)->create(['priority' => 1]);

        $this->assertNotNull($task->created_at);
        $this->assertNotNull($task->updated_at);
    }

    public function test_task_belongs_to_project(): void
    {
        $task = Task::factory()->for($this->project)->create(['priority' => 1]);

        $this->assertInstanceOf(Project::class, $task->project);
        $this->assertEquals($this->project->id, $task->project->id);
    }

    public function test_task_dates_are_cast(): void
    {
        $task = Task::factory()->for($this->project)->create([
            'priority' => 1,
            'start_date' => '2026-04-01',
            'due_date' => '2026-04-15',
        ]);

        $task->refresh();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $task->start_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $task->due_date);
    }

    public function test_task_dates_are_nullable(): void
    {
        $this->post(route('tasks.store'), [
            'name' => 'No Dates Task',
            'project_id' => $this->project->id,
        ]);

        $task = Task::where('name', 'No Dates Task')->first();
        $this->assertNull($task->start_date);
        $this->assertNull($task->due_date);
    }

    public function test_task_index_shows_dates(): void
    {
        Task::factory()->for($this->project)->create([
            'priority' => 1,
            'start_date' => '2026-04-01',
            'due_date' => '2026-04-15',
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('Apr 01, 2026');
        $response->assertSee('Apr 15, 2026');
    }
}
