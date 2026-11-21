@extends('layouts.app')

@section('title', 'Security & Audit Trail')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Audit Logs</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">System Audit Trail & Compliance</h4>
            <div class="text-muted small">Immutable historical record of security operations, updates, and access events</div>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Timestamp</th>
                            <th>User / Actor</th>
                            <th>Action</th>
                            <th>Entity Type</th>
                            <th>Entity ID</th>
                            <th>IP Address</th>
                            <th class="text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="small text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                <td>
                                    @if($log->user)
                                        <div class="fw-semibold small">{{ $log->user->name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-muted small">System / Anonymous</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-uppercase" style="font-size: 0.72rem;">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                </td>
                                <td class="small text-muted font-monospace">{{ class_basename($log->auditable_type) ?: '-' }}</td>
                                <td class="small text-muted font-monospace">#{{ $log->auditable_id ?: '-' }}</td>
                                <td class="small text-muted font-monospace">{{ $log->ip_address ?: '127.0.0.1' }}</td>
                                <td class="text-end">
                                    @if($log->old_values || $log->new_values)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                            <i class="bi bi-eye"></i> Changes
                                        </button>

                                        <!-- Log Diff Modal -->
                                        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg text-start">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold">Audit Event #{{ $log->id }} - {{ $log->action }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-danger">Previous State</label>
                                                                <pre class="bg-light p-3 rounded small mb-0" style="max-height: 250px; overflow-y: auto;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-success">Updated State</label>
                                                                <pre class="bg-light p-3 rounded small mb-0" style="max-height: 250px; overflow-y: auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No audit trail records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top d-flex justify-content-end">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
