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
            <ul class="list-group list-group-flush">
                @foreach ($projects as $project)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}">{{ $project->name }}</a>
                            <span class="badge bg-primary rounded-pill ms-2">{{ $project->tasks_count }} tasks</span>
                        </div>
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Delete this project and all its tasks?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection