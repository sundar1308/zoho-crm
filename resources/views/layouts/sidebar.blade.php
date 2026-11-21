<aside id="app-sidebar" class="app-sidebar bg-body-tertiary border-end d-flex flex-column">
    <!-- Brand Header -->
    <div class="sidebar-header p-3 border-bottom d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-body">
            <div class="brand-logo-icon bg-primary text-white rounded d-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px; font-weight: 700;">
                <i class="bi bi-clouds-fill"></i>
            </div>
            <div class="sidebar-brand-text fw-bold fs-5 tracking-tight">
                Nova<span class="text-primary">CRM</span>
            </div>
        </a>
        <button type="button" class="btn btn-sm btn-light border-0 d-md-none" id="sidebar-close-btn" aria-label="Close sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Active Organization Badge -->
    <div class="sidebar-org-banner px-3 py-2 border-bottom bg-body-secondary small d-flex align-items-center justify-content-between">
        <div class="text-truncate" title="{{ Auth::user()->organization->name ?? 'Acme Global Enterprise' }}">
            <i class="bi bi-buildings text-muted me-1"></i>
            <span class="fw-medium">{{ Auth::user()->organization->name ?? 'Acme Global Enterprise' }}</span>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Active</span>
    </div>

    <!-- Navigation Items -->
    <div class="sidebar-menu flex-grow-1 overflow-y-auto px-2 py-3">
        <ul class="nav nav-pills flex-column gap-1">
            <!-- 1. Dashboard -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard', 'home') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-heading text-uppercase text-muted px-3 mt-3 mb-1" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">Sales & Marketing</li>

            <!-- 2. Leads -->
            <li class="nav-item">
                <a href="{{ route('modules.leads') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.leads') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i>
                    <span class="nav-text">Leads</span>
                </a>
            </li>

            <!-- 3. Contacts -->
            <li class="nav-item">
                <a href="{{ route('modules.contacts') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.contacts') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">Contacts</span>
                </a>
            </li>

            <!-- 4. Accounts -->
            <li class="nav-item">
                <a href="{{ route('modules.accounts') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.accounts') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span class="nav-text">Accounts</span>
                </a>
            </li>

            <!-- 5. Deals -->
            <li class="nav-item">
                <a href="{{ route('modules.deals') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.deals') ? 'active' : '' }}">
                    <i class="bi bi-briefcase"></i>
                    <span class="nav-text">Deals</span>
                </a>
            </li>

            <!-- 6. Activities -->
            <li class="nav-item">
                <a href="{{ route('modules.activities') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.activities') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span class="nav-text">Activities</span>
                </a>
            </li>

            <!-- 7. Campaigns -->
            <li class="nav-item">
                <a href="{{ route('modules.campaigns') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.campaigns') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span class="nav-text">Campaigns</span>
                </a>
            </li>

            <li class="nav-heading text-uppercase text-muted px-3 mt-3 mb-1" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">Inventory & Quotes</li>

            <!-- 8. Products -->
            <li class="nav-item">
                <a href="{{ route('modules.products') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.products') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span class="nav-text">Products</span>
                </a>
            </li>

            <!-- 9. Price Books -->
            <li class="nav-item">
                <a href="{{ route('modules.price_books') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.price_books') ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    <span class="nav-text">Price Books</span>
                </a>
            </li>

            <!-- 10. Quotes -->
            <li class="nav-item">
                <a href="{{ route('modules.quotes') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.quotes') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span class="nav-text">Quotes</span>
                </a>
            </li>

            <!-- 11. Sales Orders -->
            <li class="nav-item">
                <a href="{{ route('modules.sales_orders') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.sales_orders') ? 'active' : '' }}">
                    <i class="bi bi-cart-check"></i>
                    <span class="nav-text">Sales Orders</span>
                </a>
            </li>

            <!-- 12. Invoices -->
            <li class="nav-item">
                <a href="{{ route('modules.invoices') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.invoices') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span class="nav-text">Invoices</span>
                </a>
            </li>

            <!-- 13. Vendors -->
            <li class="nav-item">
                <a href="{{ route('modules.vendors') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.vendors') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i>
                    <span class="nav-text">Vendors</span>
                </a>
            </li>

            <!-- 14. Purchase Orders -->
            <li class="nav-item">
                <a href="{{ route('modules.purchase_orders') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.purchase_orders') ? 'active' : '' }}">
                    <i class="bi bi-bag-check"></i>
                    <span class="nav-text">Purchase Orders</span>
                </a>
            </li>

            <li class="nav-heading text-uppercase text-muted px-3 mt-3 mb-1" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">Analytics & Operations</li>

            <!-- 15. Reports -->
            <li class="nav-item">
                <a href="{{ route('modules.reports') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.reports') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    <span class="nav-text">Reports</span>
                </a>
            </li>

            <!-- 16. Analytics -->
            <li class="nav-item">
                <a href="{{ route('modules.analytics') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.analytics') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span class="nav-text">Analytics</span>
                </a>
            </li>

            <!-- 17. Calendar -->
            <li class="nav-item">
                <a href="{{ route('modules.calendar') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.calendar') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i>
                    <span class="nav-text">Calendar</span>
                </a>
            </li>

            <!-- 18. Documents -->
            <li class="nav-item">
                <a href="{{ route('modules.documents') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.documents') ? 'active' : '' }}">
                    <i class="bi bi-folder"></i>
                    <span class="nav-text">Documents</span>
                </a>
            </li>

            <!-- 19. Automation -->
            <li class="nav-item">
                <a href="{{ route('modules.automation') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.automation') ? 'active' : '' }}">
                    <i class="bi bi-gear-wide-connected"></i>
                    <span class="nav-text">Automation</span>
                </a>
            </li>

            <!-- 20. Portals -->
            <li class="nav-item">
                <a href="{{ route('modules.portals') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.portals') ? 'active' : '' }}">
                    <i class="bi bi-door-open"></i>
                    <span class="nav-text">Portals</span>
                </a>
            </li>

            <!-- 21. Integrations -->
            <li class="nav-item">
                <a href="{{ route('modules.integrations') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.integrations') ? 'active' : '' }}">
                    <i class="bi bi-puzzle"></i>
                    <span class="nav-text">Integrations</span>
                </a>
            </li>

            <li class="nav-heading text-uppercase text-muted px-3 mt-3 mb-1" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">Administration</li>

            <!-- 22. Settings & Admin Submenus -->
            <li class="nav-item">
                <a href="{{ route('settings.users.index') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('settings.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    <span class="nav-text">User Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('settings.roles.index') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('settings.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i>
                    <span class="nav-text">Roles & Permissions</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('settings.teams.index') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('settings.teams.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i>
                    <span class="nav-text">Teams</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('audit_logs.index') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('audit_logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span class="nav-text">Audit Logs</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('modules.settings') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('modules.settings') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i>
                    <span class="nav-text">Settings Hub</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Footer User Card -->
    <div class="sidebar-footer p-3 border-top mt-auto bg-body-tertiary">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-semibold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="overflow-hidden flex-grow-1">
                <div class="fw-semibold text-truncate small">{{ Auth::user()->name ?? 'User' }}</div>
                <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ Auth::user()->roles->first()->name ?? (Auth::user()->is_super_admin ? 'Super Admin' : 'Member') }}</div>
            </div>
            <a href="{{ route('profile.index') }}" class="text-secondary" title="View Profile">
                <i class="bi bi-gear"></i>
            </a>
        </div>
    </div>
</aside>
