<header class="app-topbar bg-body border-bottom px-3 py-2 sticky-top">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <!-- Left Section: Toggle & Global Search -->
        <div class="d-flex align-items-center gap-3 flex-grow-1" style="max-width: 600px;">
            <button class="btn btn-sm btn-outline-secondary border-0" id="sidebar-toggle-btn" title="Toggle Sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>

            <!-- Global Search Form -->
            <div class="position-relative w-100">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-body-tertiary border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="global-search-input" class="form-control bg-body-tertiary border-start-0 ps-0" placeholder="Search leads, contacts, deals, accounts, quotes..." aria-label="Global Search">
                    <span class="input-group-text bg-body-tertiary text-muted d-none d-md-inline" style="font-size: 0.75rem;">Ctrl+K</span>
                </div>
            </div>
        </div>

        <!-- Right Section: Shortcuts, Tenant Selector, Notifications & Profile -->
        <div class="d-flex align-items-center gap-2">
            <!-- Organization Switcher -->
            @php
                $userOrgs = Auth::user()->organizations;
                $currentOrg = Auth::user()->organization;
            @endphp
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Switch Organization">
                    <i class="bi bi-buildings"></i>
                    <span class="d-none d-lg-inline text-truncate" style="max-width: 140px;">{{ $currentOrg->name ?? 'Acme Global' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header">Switch Organization</h6></li>
                    @if($userOrgs && $userOrgs->count() > 0)
                        @foreach($userOrgs as $orgItem)
                            <li>
                                <form action="{{ route('organizations.switch', $orgItem->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between {{ ($currentOrg && $currentOrg->id == $orgItem->id) ? 'active' : '' }}">
                                        <span>{{ $orgItem->name }}</span>
                                        @if($currentOrg && $currentOrg->id == $orgItem->id)
                                            <i class="bi bi-check-lg ms-2"></i>
                                        @endif
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    @else
                        <li><span class="dropdown-item-text text-muted small">No other organizations</span></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('organizations.index') }}"><i class="bi bi-plus-circle me-1"></i> Manage Organizations</a></li>
                </ul>
            </div>

            <!-- Quick Create Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle d-flex align-items-center gap-1 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-sm-inline">Quick Create</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header">New Record</h6></li>
                    <li><a class="dropdown-item" href="{{ route('modules.leads') }}"><i class="bi bi-person-plus text-primary me-2"></i> Create Lead</a></li>
                    <li><a class="dropdown-item" href="{{ route('modules.contacts') }}"><i class="bi bi-people text-info me-2"></i> Create Contact</a></li>
                    <li><a class="dropdown-item" href="{{ route('modules.accounts') }}"><i class="bi bi-building text-warning me-2"></i> Create Account</a></li>
                    <li><a class="dropdown-item" href="{{ route('modules.deals') }}"><i class="bi bi-briefcase text-success me-2"></i> Create Deal</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('modules.activities') }}"><i class="bi bi-check2-square text-danger me-2"></i> Add Task / Activity</a></li>
                </ul>
            </div>

            <!-- Calendar Shortcut -->
            <a href="{{ route('modules.calendar') }}" class="btn btn-sm btn-outline-secondary border-0 position-relative" title="Calendar">
                <i class="bi bi-calendar3"></i>
            </a>

            <!-- Tasks Shortcut -->
            <a href="{{ route('modules.activities') }}" class="btn btn-sm btn-outline-secondary border-0 position-relative" title="Tasks">
                <i class="bi bi-check2-circle"></i>
            </a>

            <!-- Notifications Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary border-0 position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-sm p-0" style="width: 320px;">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold">Notifications</h6>
                        <span class="badge bg-primary-subtle text-primary">3 New</span>
                    </div>
                    <div class="list-group list-group-flush small" style="max-height: 240px; overflow-y: auto;">
                        <a href="{{ route('modules.activities') }}" class="list-group-item list-group-item-action py-2">
                            <div class="d-flex w-100 justify-content-between">
                                <strong class="mb-1 text-primary">Upcoming Task Due</strong>
                                <small class="text-muted">In 2 days</small>
                            </div>
                            <p class="mb-1 text-truncate">Send revised master service agreement to Lucius Fox</p>
                        </a>
                        <a href="{{ route('modules.leads') }}" class="list-group-item list-group-item-action py-2">
                            <div class="d-flex w-100 justify-content-between">
                                <strong class="mb-1 text-success">New Lead Ingested</strong>
                                <small class="text-muted">Today</small>
                            </div>
                            <p class="mb-1 text-truncate">Diana Prince from Themyscira Foundation</p>
                        </a>
                        <a href="{{ route('modules.deals') }}" class="list-group-item list-group-item-action py-2">
                            <div class="d-flex w-100 justify-content-between">
                                <strong class="mb-1 text-info">Stage Transition</strong>
                                <small class="text-muted">Yesterday</small>
                            </div>
                            <p class="mb-1 text-truncate">Stark Industries Enterprise Rollout closed as Won</p>
                        </a>
                    </div>
                    <div class="p-2 border-top text-center">
                        <a href="{{ route('audit_logs.index') }}" class="text-decoration-none small text-muted">View all activity logs</a>
                    </div>
                </div>
            </div>

            <!-- Dark / Light Theme Toggle -->
            <button class="btn btn-sm btn-outline-secondary border-0" id="theme-toggle-btn" title="Toggle Light/Dark Theme">
                <i class="bi bi-moon-stars" id="theme-icon"></i>
            </button>

            <!-- Help Shortcut -->
            <button type="button" class="btn btn-sm btn-outline-secondary border-0" data-bs-toggle="modal" data-bs-target="#helpModal" title="Help & Documentation">
                <i class="bi bi-question-circle"></i>
            </button>

            <!-- User Profile Dropdown -->
            <div class="dropdown ms-1">
                <button class="btn btn-sm p-0 border-0 rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                        </div>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-semibold text-truncate">{{ Auth::user()->name }}</div>
                        <div class="text-muted small text-truncate">{{ Auth::user()->email }}</div>
                        <span class="badge bg-secondary-subtle text-secondary border mt-1">{{ Auth::user()->roles->first()->name ?? (Auth::user()->is_super_admin ? 'Super Admin' : 'Member') }}</span>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i> My Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('settings.users.index') }}"><i class="bi bi-gear me-2"></i> Admin Settings</a></li>
                    <li><a class="dropdown-item" href="{{ route('audit_logs.index') }}"><i class="bi bi-shield-check me-2"></i> Audit Trail</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel"><i class="bi bi-info-circle text-primary me-2"></i>Nova CRM Platform Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Keyboard Shortcuts</h6>
                <ul class="list-unstyled small mb-3">
                    <li><kbd>Ctrl</kbd> + <kbd>K</kbd> : Focus global search</li>
                    <li><kbd>Alt</kbd> + <kbd>D</kbd> : Jump to Dashboard</li>
                </ul>
                <h6>System Documentation</h6>
                <p class="small text-muted mb-0">
                    Comprehensive architecture, ER diagrams, module specifications, and API documentation are maintained locally in the repository root (e.g. <code>PROJECT_ARCHITECTURE.md</code>, <code>DATABASE_SCHEMA.md</code>, <code>API_DOCUMENTATION.md</code>).
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
