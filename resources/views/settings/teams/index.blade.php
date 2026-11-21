@extends('layouts.app')

@section('title', 'Teams & Departments')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active" aria-current="page">Teams</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">Teams & Department Hierarchies</h4>
            <div class="text-muted small">Group users into regional sales squads and functional departments</div>
        </div>

        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createTeamModal">
            <i class="bi bi-plus-lg"></i>
            <span>Create Team</span>
        </button>
    </div>

    <!-- Teams Grid -->
    <div class="row g-3">
        @forelse($teams as $team)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-body border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold">{{ $team->name }}</h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editTeamModal{{ $team->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <p class="small text-muted mb-3">{{ $team->description ?? 'Functional department unit.' }}</p>

                        <div class="small mb-2">
                            <strong>Team Lead:</strong>
                            <span class="text-primary">{{ $team->leader->name ?? 'None' }}</span>
                        </div>

                        <div class="small">
                            <strong>Members ({{ $team->members->count() }}):</strong>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @forelse($team->members as $m)
                                    <span class="badge bg-light text-dark border">{{ $m->name }}</span>
                                @empty
                                    <span class="text-muted small">No members assigned</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Team Modal -->
            <div class="modal fade" id="editTeamModal{{ $team->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('settings.teams.update', $team->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold">Edit Team: {{ $team->name }}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Team Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $team->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $team->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Team Leader</label>
                                    <select name="leader_id" class="form-select">
                                        <option value="">Select Leader...</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ $team->leader_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Assign Members</label>
                                    <select name="members[]" class="form-select" multiple size="5">
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ $team->members->contains('id', $u->id) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text small">Hold Ctrl (Windows) / Cmd (Mac) to select multiple users.</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-4 text-center text-muted">No teams created yet.</div>
            </div>
        @endforelse
    </div>
</div>

<!-- Create Team Modal -->
<div class="modal fade" id="createTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.teams.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Create New Team</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Team Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Inbound Marketing Squad">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Responsibilities and scope..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Team Leader</label>
                        <select name="leader_id" class="form-select">
                            <option value="">Select Leader...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Initial Members</label>
                        <select name="members[]" class="form-select" multiple size="4">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Create Team</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
