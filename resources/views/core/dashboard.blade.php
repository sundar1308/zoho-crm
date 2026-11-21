@extends('layouts.app')

@section('title', 'Executive Sales Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Page Header & Filter Toolbar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">Executive CRM Dashboard</h4>
            <div class="text-muted small">Live analytics and performance metrics for {{ Auth::user()->organization->name ?? 'Acme Global Enterprise' }}</div>
        </div>

        <!-- Filter Controls -->
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
            <!-- Date Range Selector -->
            <div class="btn-group btn-group-sm shadow-sm" role="group" aria-label="Date Range Filters">
                <a href="{{ route('dashboard', array_merge(request()->query(), ['range' => 'today'])) }}" class="btn btn-outline-secondary {{ $dateRange === 'today' ? 'active' : '' }}">Today</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['range' => 'week'])) }}" class="btn btn-outline-secondary {{ $dateRange === 'week' ? 'active' : '' }}">This Week</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['range' => 'month'])) }}" class="btn btn-outline-secondary {{ $dateRange === 'month' ? 'active' : '' }}">This Month</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['range' => 'quarter'])) }}" class="btn btn-outline-secondary {{ $dateRange === 'quarter' ? 'active' : '' }}">Quarter</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['range' => 'year'])) }}" class="btn btn-outline-secondary {{ $dateRange === 'year' ? 'active' : '' }}">YTD</a>
            </div>

            <!-- User Filter -->
            <select name="user_id" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                <option value="">All Users</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>

            <!-- Team Filter -->
            <select name="team_id" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                <option value="">All Teams</option>
                @foreach($teams as $t)
                    <option value="{{ $t->id }}" {{ $teamId == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>

            <!-- Export Button -->
            <a href="{{ route('dashboard.export', ['range' => $dateRange]) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-download"></i>
                <span>Export CSV</span>
            </a>
        </form>
    </div>

    <!-- Metrics Cards Row 1: Leads & Customer Pipeline -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Leads Overview -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3 bg-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Leads</span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                        <i class="bi bi-person-plus fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($metrics['total_leads']) }}</h3>
                <div class="d-flex align-items-center gap-2 small text-muted">
                    <span class="badge bg-success-subtle text-success">{{ $metrics['new_leads'] }} New</span>
                    <span class="badge bg-info-subtle text-info">{{ $metrics['qualified_leads'] }} Qualified</span>
                </div>
                <div class="mt-2 pt-2 border-top small text-muted d-flex justify-content-between">
                    <span>Conversion Rate</span>
                    <strong class="text-success">{{ $metrics['lead_conversion_rate'] }}%</strong>
                </div>
            </div>
        </div>

        <!-- Card 2: Contacts & Accounts -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3 bg-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Accounts & Contacts</span>
                    <div class="bg-info-subtle text-info p-2 rounded-circle">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($metrics['total_accounts']) }} <span class="fs-6 text-muted fw-normal">Accounts</span></h3>
                <div class="d-flex align-items-center gap-2 small text-muted">
                    <span class="badge bg-secondary-subtle text-secondary">{{ number_format($metrics['total_contacts']) }} Contacts</span>
                    <span class="badge bg-light text-dark border">Enterprise Active</span>
                </div>
                <div class="mt-2 pt-2 border-top small text-muted d-flex justify-content-between">
                    <span>Account Health</span>
                    <strong class="text-primary">100% Retained</strong>
                </div>
            </div>
        </div>

        <!-- Card 3: Pipeline Value & Expected Revenue -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3 bg-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Active Pipeline</span>
                    <div class="bg-warning-subtle text-warning p-2 rounded-circle">
                        <i class="bi bi-briefcase fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">${{ number_format($metrics['pipeline_value'], 2) }}</h3>
                <div class="d-flex align-items-center gap-2 small text-muted">
                    <span class="badge bg-warning-subtle text-warning">{{ $metrics['open_deals'] }} Open Deals</span>
                    <span>Weighted: ${{ number_format($metrics['expected_revenue'], 0) }}</span>
                </div>
                <div class="mt-2 pt-2 border-top small text-muted d-flex justify-content-between">
                    <span>Closed Win Rate</span>
                    <strong class="text-success">{{ $metrics['win_rate'] }}%</strong>
                </div>
            </div>
        </div>

        <!-- Card 4: Monthly Won Revenue & Target -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100 p-3 bg-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Won Revenue</span>
                    <div class="bg-success-subtle text-success p-2 rounded-circle">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-success">${{ number_format($metrics['monthly_revenue'], 2) }}</h3>
                <div class="d-flex align-items-center gap-2 small text-muted">
                    <span class="badge bg-success-subtle text-success">{{ $metrics['won_deals'] }} Won</span>
                    <span class="badge bg-danger-subtle text-danger">{{ $metrics['lost_deals'] }} Lost</span>
                </div>
                <div class="mt-2 pt-2 border-top small text-muted d-flex justify-content-between">
                    <span>Target Achievement</span>
                    <strong class="{{ $metrics['target_achievement'] >= 100 ? 'text-success' : 'text-primary' }}">{{ $metrics['target_achievement'] }}%</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Monthly Revenue Trend & Deals by Stage Funnel -->
    <div class="row g-3 mb-4">
        <!-- Monthly Revenue Trend Chart -->
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-body border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="bi bi-graph-up text-primary me-2"></i>Sales Revenue Performance (Last 6 Months)</h6>
                    <span class="badge bg-primary-subtle text-primary">Closed Won</span>
                </div>
                <div class="card-body p-3">
                    <canvas id="monthlyRevenueChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Deals By Stage Chart -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-body border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="bi bi-funnel text-warning me-2"></i>Deals by Stage</h6>
                    <span class="badge bg-warning-subtle text-warning">Pipeline</span>
                </div>
                <div class="card-body p-3">
                    <canvas id="dealsByStageChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Leads by Source, Leads by Status, Won vs Lost -->
    <div class="row g-3 mb-4">
        <!-- Leads by Source -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-body border-bottom py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-pie-chart text-info me-2"></i>Leads by Source</h6>
                </div>
                <div class="card-body p-3 d-flex align-items-center justify-content-center">
                    <canvas id="leadsBySourceChart" style="max-height: 240px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Leads by Status -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-body border-bottom py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-bar-chart-line text-success me-2"></i>Leads by Status</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="leadsByStatusChart" style="max-height: 240px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Won vs Lost Comparison -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-body border-bottom py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-award text-primary me-2"></i>Won vs Lost Opportunities</h6>
                </div>
                <div class="card-body p-3 d-flex align-items-center justify-content-center">
                    <canvas id="wonVsLostChart" style="max-height: 240px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities & Task Worklist Row -->
    <div class="row g-3">
        <!-- Recent Activities Timeline -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-body border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="bi bi-activity text-danger me-2"></i>Recent CRM Activities & Timeline</h6>
                    <a href="{{ route('modules.activities') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metrics['recent_activities'] as $act)
                                    <tr>
                                        <td>
                                            @if($act->activity_type === 'task')
                                                <span class="badge bg-primary-subtle text-primary"><i class="bi bi-check2-square me-1"></i>Task</span>
                                            @elseif($act->activity_type === 'call')
                                                <span class="badge bg-success-subtle text-success"><i class="bi bi-telephone me-1"></i>Call</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning"><i class="bi bi-calendar-event me-1"></i>Meeting</span>
                                            @endif
                                        </td>
                                        <td class="fw-medium text-truncate" style="max-width: 250px;">{{ $act->subject }}</td>
                                        <td class="small">{{ $act->assignedUser->name ?? 'Unassigned' }}</td>
                                        <td>
                                            @if($act->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($act->status === 'in_progress')
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            @else
                                                <span class="badge bg-secondary">Not Started</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">
                                            {{ $act->due_date ? $act->due_date->format('M d, Y') : ($act->start_time ? $act->start_time->format('M d, H:i') : '-') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No recent activities found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operational Status & Tasks Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-body border-bottom py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-list-check text-info me-2"></i>Action Items Summary</h6>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="bi bi-hourglass-split text-warning me-2"></i>Pending Tasks</span>
                            <span class="badge bg-warning-subtle text-warning fs-6">{{ $metrics['pending_tasks'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="bi bi-exclamation-circle text-danger me-2"></i>Overdue Tasks</span>
                            <span class="badge bg-danger-subtle text-danger fs-6">{{ $metrics['overdue_tasks'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="bi bi-calendar3 text-info me-2"></i>Upcoming Meetings</span>
                            <span class="badge bg-info-subtle text-info fs-6">{{ $metrics['upcoming_meetings'] }}</span>
                        </div>
                    </div>

                    <div class="mt-3 p-3 bg-body-tertiary rounded">
                        <div class="small fw-bold text-muted mb-1">Quick Action</div>
                        <a href="{{ route('modules.activities') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                            <i class="bi bi-plus-circle me-1"></i> Create New Activity
                        </a>
                        <a href="{{ route('modules.leads') }}" class="btn btn-sm btn-outline-success w-100">
                            <i class="bi bi-person-plus me-1"></i> Ingest New Lead
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartData = @json($chartData);

    // 1. Monthly Revenue Chart (Line Chart)
    const revCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: chartData.monthly_revenue.labels,
            datasets: [{
                label: 'Won Revenue ($)',
                data: chartData.monthly_revenue.values,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (val) => '$' + Number(val).toLocaleString()
                    }
                }
            }
        }
    });

    // 2. Deals by Stage (Horizontal Bar Chart)
    const stageCtx = document.getElementById('dealsByStageChart').getContext('2d');
    new Chart(stageCtx, {
        type: 'bar',
        data: {
            labels: chartData.deals_by_stage.labels,
            datasets: [{
                label: 'Deals Count',
                data: chartData.deals_by_stage.counts,
                backgroundColor: chartData.deals_by_stage.colors.length ? chartData.deals_by_stage.colors : '#0d6efd',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // 3. Leads by Source (Doughnut Chart)
    const sourceCtx = document.getElementById('leadsBySourceChart').getContext('2d');
    new Chart(sourceCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.leads_by_source.labels.length ? chartData.leads_by_source.labels : ['No Data'],
            datasets: [{
                data: chartData.leads_by_source.values.length ? chartData.leads_by_source.values : [0],
                backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#6f42c1', '#d63384']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
            }
        }
    });

    // 4. Leads by Status (Bar Chart)
    const statusCtx = document.getElementById('leadsByStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: chartData.leads_by_status.labels,
            datasets: [{
                label: 'Leads',
                data: chartData.leads_by_status.values,
                backgroundColor: chartData.leads_by_status.colors.length ? chartData.leads_by_status.colors : '#198754',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 5. Won vs Lost Comparison (Doughnut / Pie)
    const wonLostCtx = document.getElementById('wonVsLostChart').getContext('2d');
    new Chart(wonLostCtx, {
        type: 'pie',
        data: {
            labels: ['Closed Won', 'Closed Lost'],
            datasets: [{
                data: [chartData.won_vs_lost.won, chartData.won_vs_lost.lost],
                backgroundColor: ['#198754', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } }
            }
        }
    });
});
</script>
@endpush
