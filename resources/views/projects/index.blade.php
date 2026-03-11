@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Projects</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">New Project</a>
    </div>

    @if ($projects->isEmpty())
        <div class="alert alert-info">No projects yet. Create one to get started.</div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Project Name</th>
                            <th style="width: 100px;">Tasks</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}">
                                        <strong>{{ $project->name }}</strong>
                                    </a>
                                </td>
                                <td><span class="badge bg-primary rounded-pill">{{ $project->tasks_count }}</span></td>
                                <td><small class="text-muted">{{ $project->created_at->format('M d, Y H:i') }}</small></td>
                                <td><small class="text-muted">{{ $project->updated_at->format('M d, Y H:i') }}</small></td>
                                <td>
                                    <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Delete this project and all its tasks?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
