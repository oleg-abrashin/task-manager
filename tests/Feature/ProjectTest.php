<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_index_displays_projects(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.index'));

        $response->assertOk();
        $response->assertSee($project->name);
    }

    public function test_project_index_shows_task_count(): void
    {
        $project = Project::factory()->create();
        Task::factory(3)->for($project)->sequence(
            fn ($seq) => ['priority' => $seq->index + 1],
        )->create();

        $response = $this->get(route('projects.index'));

        $response->assertOk();
        $response->assertSee('>3</span>', false);
    }

    public function test_project_create_form_displays(): void
    {
        $response = $this->get(route('projects.create'));

        $response->assertOk();
        $response->assertSee('Create Project');
    }

    public function test_project_can_be_stored(): void
    {
        $response = $this->post(route('projects.store'), ['name' => 'New Project']);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }

    public function test_project_store_validates_name(): void
    {
        $response = $this->post(route('projects.store'), ['name' => '']);

        $response->assertSessionHasErrors('name');
    }

    public function test_project_name_must_be_unique(): void
    {
        Project::factory()->create(['name' => 'Existing']);

        $response = $this->post(route('projects.store'), ['name' => 'Existing']);

        $response->assertSessionHasErrors('name');
    }

    public function test_project_can_be_deleted(): void
    {
        $project = Project::factory()->create();

        $response = $this->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_deleting_project_cascades_to_tasks(): void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->for($project)->create(['priority' => 1]);

        $this->delete(route('projects.destroy', $project));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_project_has_tasks_relationship(): void
    {
        $project = Project::factory()->create();
        Task::factory()->for($project)->create(['priority' => 1]);

        $this->assertCount(1, $project->tasks);
        $this->assertInstanceOf(Task::class, $project->tasks->first());
    }
}
