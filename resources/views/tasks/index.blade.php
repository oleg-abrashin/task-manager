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
            <ul class="list-group list-group-flush" id="task-list">
                @foreach ($tasks as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $task->id }}">
                        <div class="d-flex align-items-center gap-3">
                            <span class="handle cursor-grab" style="cursor: grab;">&#9776;</span>
                            <span class="badge bg-secondary">{{ $task->priority }}</span>
                            <span>{{ $task->name }}</span>
                            <small class="text-muted">{{ $task->project->name }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const list = document.getElementById('task-list');
        if (!list) return;

        Sortable.create(list, {
            handle: '.handle',
            animation: 150,
            onEnd: function () {
                const ids = Array.from(list.children).map(el => el.dataset.id);

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
                    list.querySelectorAll('.badge').forEach((badge, index) => {
                        badge.textContent = index + 1;
                    });
                });
            },
        });
    });
</script>
@endpush