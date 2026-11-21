@extends('layouts.app')

@section('title', 'Roles & Permission Management')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active" aria-current="page">Roles & Permissions</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">Role-Based Access Control (RBAC)</h4>
            <div class="text-muted small">Configure security roles and granular module permission levels</div>
        </div>

        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-shield-plus"></i>
            <span>Create Custom Role</span>
        </button>
    </div>

    <!-- Roles Grid -->
    <div class="row g-3 mb-4">
        @foreach($roles as $role)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-body border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="m-0 fw-bold">{{ $role->name }}</h6>
                            <span class="badge {{ $role->is_system ? 'bg-secondary-subtle text-secondary' : 'bg-primary-subtle text-primary' }} small mt-1">
                                {{ $role->is_system ? 'System Default' : 'Custom Role' }}
                            </span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#permissionsModal{{ $role->id }}">
                            <i class="bi bi-key me-1"></i> Permissions ({{ $role->permissions->count() }})
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <p class="small text-muted mb-2">{{ $role->description ?? 'Standard system role for organization staff.' }}</p>
                        <div class="small">
                            <strong>Assigned Members:</strong> {{ $role->users()->count() }} users
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Matrix Modal -->
            <div class="modal fade" id="permissionsModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <form action="{{ route('settings.roles.update', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold">Manage Privileges: {{ $role->name }}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Role Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" {{ $role->is_system ? 'readonly' : '' }} required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $role->description }}</textarea>
                                </div>

                                <h6 class="fw-bold mb-2">Module Permission Matrix</h6>
                                <div class="table-responsive border rounded">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>Module</th>
                                                <th>Granted Privileges</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permissionsByModule as $moduleName => $perms)
                                                <tr>
                                                    <td class="fw-semibold text-uppercase small" style="width: 140px;">{{ $moduleName }}</td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($perms as $p)
                                                                <div class="form-check form-check-inline m-0">
                                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="perm_{{ $role->id }}_{{ $p->id }}" {{ $role->permissions->contains('id', $p->id) ? 'checked' : '' }}>
                                                                    <label class="form-check-label small" for="perm_{{ $role->id }}_{{ $p->id }}">
                                                                        {{ explode('.', $p->name)[1] ?? $p->name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-primary">Update Permissions</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Create Custom Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.roles.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Create Custom Security Role</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Role Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Regional Deal Specialist">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Outline duties and privileges..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
