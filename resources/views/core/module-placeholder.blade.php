@extends('layouts.app')

@section('title', $moduleTitle)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $moduleTitle }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">{{ $moduleTitle }} Workspace</h4>
            <div class="text-muted small">Active Organization: {{ Auth::user()->organization->name ?? 'Acme Global Enterprise' }}</div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="Swal.fire({title: 'Import Data', text: 'Import CSV/XLSX pipeline will be active in Phase 2/6.', icon: 'info'})">
                <i class="bi bi-upload me-1"></i> Import
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="Swal.fire({title: 'Export Data', text: 'Export pipeline will be active in Phase 2/6.', icon: 'info'})">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm" onclick="Swal.fire({title: 'Create {{ rtrim($moduleTitle, 's') }}', text: 'Detailed entity CRUD forms are scheduled in the next phase roadmap.', icon: 'info'})">
                <i class="bi bi-plus-lg"></i>
                <span>Add {{ rtrim($moduleTitle, 's') }}</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search {{ strtolower($moduleTitle) }} records...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm">
                        <option selected>All {{ $moduleTitle }} (Default View)</option>
                        <option>My {{ $moduleTitle }}</option>
                        <option>Recently Created</option>
                        <option>Modified This Week</option>
                    </select>
                </div>
                <div class="col-md-5 text-md-end">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                        <i class="bi bi-info-circle me-1"></i> Foundation initialized in Phase 1 &bull; Full CRUD scheduled for Phase 2
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary / Empty State Card -->
    <div class="card shadow-sm border-0 text-center py-5">
        <div class="card-body py-4">
            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 64px; height: 64px; font-size: 1.8rem;">
                <i class="bi bi-folder2-open"></i>
            </div>
            <h5 class="fw-bold mb-1">{{ $moduleTitle }} Module Ready</h5>
            <p class="text-muted small mx-auto mb-3" style="max-width: 500px;">
                The database tables, models, relationships, and authorization gates for <strong>{{ $moduleTitle }}</strong> have been constructed. Data from this module is currently feeding the executive dashboard aggregations.
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-speedometer2 me-1"></i> Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
