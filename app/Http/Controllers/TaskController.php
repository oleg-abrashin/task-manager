<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::orderBy('name')->get();

        $tasks = Task::with('project')
            ->when($request->filled('project_id'), fn ($query) => $query->where('project_id', $request->project_id))
            ->orderBy('priority')
            ->get();

        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function create(): View
    {
        $projects = Project::orderBy('name')->get();

        return view('tasks.create', compact('projects'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['priority'])) {
            $data['priority'] = Task::where('project_id', $data['project_id'])->max('priority') + 1;
        }

        Task::create($data);

        return redirect()->route('tasks.index', ['project_id' => $data['project_id']])
            ->with('success', 'Task created.');
    }

    public function edit(Task $task): View
    {
        $projects = Project::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('tasks.index', ['project_id' => $task->project_id])
            ->with('success', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;
        $task->delete();

        $this->reorderTasks($projectId);

        return redirect()->route('tasks.index', ['project_id' => $projectId])
            ->with('success', 'Task deleted.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:tasks,id'],
        ]);

        foreach ($request->ids as $priority => $id) {
            Task::where('id', $id)->update(['priority' => $priority + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function reorderTasks(int $projectId): void
    {
        Task::where('project_id', $projectId)
            ->orderBy('priority')
            ->get()
            ->each(fn (Task $task, int $index) => $task->update(['priority' => $index + 1]));
    }
}
