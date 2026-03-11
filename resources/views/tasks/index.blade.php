@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Tasks</h1>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">New Task</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tasks.index') }}" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label for="project_id" class="form-label">Filter by Project</label>
                    <select name="project_id" id="project_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if (request('project_id'))
                    <div class="col-auto">
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Clear Filter</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if ($tasks->isEmpty())
        <div class="alert alert-info">No tasks yet. Create one to get started.</div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th style="width: 60px;">#</th>
                            <th>Task Name</th>
                            <th>Project</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Created</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="task-list">
                        @foreach ($tasks as $task)
                            <tr data-id="{{ $task->id }}">
                                <td><span class="handle" style="cursor: grab;">&#9776;</span></td>
                                <td><span class="badge bg-secondary">{{ $task->priority }}</span></td>
                                <td>{{ $task->name }}</td>
                                <td><small class="text-muted">{{ $task->project->name }}</small></td>
                                <td>{{ $task->start_date?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $task->due_date?->format('M d, Y') ?? '—' }}</td>
                                <td><small class="text-muted">{{ $task->created_at->format('M d, Y') }}</small></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tbody = document.getElementById('task-list');
        if (!tbody) return;

        Sortable.create(tbody, {
            handle: '.handle',
            animation: 150,
            onEnd: function () {
                const ids = Array.from(tbody.children).map(tr => tr.dataset.id);

                fetch('{{ route('tasks.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ids }),
                })
                .then(response => response.json())
                .then(() => {
                    tbody.querySelectorAll('.badge').forEach((badge, index) => {
                        badge.textContent = index + 1;
                    });
                });
            },
        });
    });
</script>
@endpush
