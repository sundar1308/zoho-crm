@extends('layouts.app')

@section('title', 'Organization Workspaces')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active" aria-current="page">Organizations</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">Multi-Tenant Organizations</h4>
            <div class="text-muted small">Manage enterprise organizations and active workspace isolation</div>
        </div>

        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createOrgModal">
            <i class="bi bi-plus-lg"></i>
            <span>New Organization</span>
        </button>
    </div>

    <!-- Organizations Grid -->
    <div class="row g-3">
        @foreach($organizations as $org)
            @php
                $isActiveTenant = (session('active_organization_id') == $org->id) || (!session('active_organization_id') && Auth::user()->organization_id == $org->id);
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 {{ $isActiveTenant ? 'border border-primary' : '' }}">
                    <div class="card-header bg-body border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary-subtle text-primary p-2 rounded">
                                <i class="bi bi-buildings"></i>
                            </div>
                            <h6 class="m-0 fw-bold">{{ $org->name }}</h6>
                        </div>
                        @if($isActiveTenant)
                            <span class="badge bg-primary">Active</span>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <div class="small text-muted mb-2"><strong>Domain:</strong> {{ $org->domain ?? 'N/A' }}</div>
                        <div class="small text-muted mb-2"><strong>Currency / Timezone:</strong> {{ $org->currency }} / {{ $org->timezone }}</div>
                        <div class="small text-muted mb-3"><strong>Users Count:</strong> {{ $org->users_count ?? 1 }} members</div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            @if(!$isActiveTenant)
                                <form action="{{ route('organizations.switch', $org->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-repeat me-1"></i> Switch Workspace
                                    </button>
                                </form>
                            @else
                                <span class="text-success small fw-semibold"><i class="bi bi-check2-circle me-1"></i>Current Workspace</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Create Organization Modal -->
<div class="modal fade" id="createOrgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('organizations.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Create New Organization</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Organization Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Wayne Enterprises Global">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Corporate Domain</label>
                        <input type="text" name="domain" class="form-control" placeholder="wayne.example.com">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Currency</label>
                            <select name="currency" class="form-select" required>
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="CAD">CAD ($)</option>
                                <option value="AUD">AUD ($)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Timezone</label>
                            <select name="timezone" class="form-select" required>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New_York</option>
                                <option value="America/Chicago">America/Chicago</option>
                                <option value="America/Los_Angeles">America/Los_Angeles</option>
                                <option value="Europe/London">Europe/London</option>
                                <option value="Asia/Tokyo">Asia/Tokyo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Create Workspace</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
