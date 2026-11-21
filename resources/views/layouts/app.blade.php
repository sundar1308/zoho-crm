<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | Nova CRM Platform</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css">

    <!-- Custom Enterprise CRM Styles -->
    <style>
        :root {
            --crm-sidebar-width: 255px;
            --crm-sidebar-collapsed-width: 72px;
            --crm-topbar-height: 58px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .app-sidebar {
            width: var(--crm-sidebar-width);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1040;
            transition: all 0.25s ease-in-out;
        }

        .app-sidebar .nav-link {
            color: var(--bs-secondary-color);
            border-radius: 0.375rem;
            padding: 0.55rem 0.85rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .app-sidebar .nav-link:hover {
            color: var(--bs-primary);
            background-color: var(--bs-tertiary-bg);
        }

        .app-sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--bs-primary);
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
        }

        .app-sidebar .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Collapsed Sidebar */
        body.sidebar-collapsed .app-sidebar {
            width: var(--crm-sidebar-collapsed-width);
        }

        body.sidebar-collapsed .app-sidebar .nav-text,
        body.sidebar-collapsed .app-sidebar .sidebar-brand-text,
        body.sidebar-collapsed .app-sidebar .nav-heading,
        body.sidebar-collapsed .app-sidebar .sidebar-org-banner {
            display: none !important;
        }

        body.sidebar-collapsed .app-sidebar .nav-link {
            justify-content: center;
            padding: 0.6rem;
        }

        /* Main Wrapper */
        .app-main-wrapper {
            margin-left: var(--crm-sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s ease-in-out;
        }

        body.sidebar-collapsed .app-main-wrapper {
            margin-left: var(--crm-sidebar-collapsed-width);
        }

        /* Topbar */
        .app-topbar {
            height: var(--crm-topbar-height);
            z-index: 1020;
        }

        /* Stat Widget Cards */
        .stat-card {
            border: 1px solid var(--bs-border-color-translucent);
            border-radius: 0.5rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(150, 150, 150, 0.4);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(150, 150, 150, 0.7);
        }

        /* Responsive Mobile Drawer */
        @media (max-width: 767.98px) {
            .app-sidebar {
                margin-left: calc(-1 * var(--crm-sidebar-width));
            }
            body.sidebar-mobile-open .app-sidebar {
                margin-left: 0;
            }
            .app-main-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Workspace -->
        <div class="app-main-wrapper flex-grow-1">
            <!-- Topbar Header -->
            @include('layouts.topbar')

            <!-- Main Content Area -->
            <main class="app-content p-3 p-md-4 flex-grow-1">
                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>{{ session('info') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Please check the form errors below:</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="app-footer border-top px-4 py-2 bg-body text-muted small d-flex justify-content-between align-items-center">
                <span>&copy; {{ date('Y') }} Nova CRM Platform. Enterprise Edition.</span>
                <span>Active Organization: <strong>{{ Auth::user()->organization->name ?? 'Acme Global Enterprise' }}</strong></span>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>

    <!-- Global App Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Theme Switcher (Light / Dark)
            const htmlElement = document.documentElement;
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            const themeIcon = document.getElementById('theme-icon');

            const savedTheme = localStorage.getItem('novacrm_theme') || 'light';
            setTheme(savedTheme);

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(nextTheme);
                });
            }

            function setTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('novacrm_theme', theme);
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.classList.remove('bi-moon-stars');
                        themeIcon.classList.add('bi-sun');
                    } else {
                        themeIcon.classList.remove('bi-sun');
                        themeIcon.classList.add('bi-moon-stars');
                    }
                }
            }

            // 2. Sidebar Toggle (Desktop Collapse & Mobile Drawer)
            const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
            const sidebarCloseBtn = document.getElementById('sidebar-close-btn');

            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', () => {
                    if (window.innerWidth < 768) {
                        document.body.classList.toggle('sidebar-mobile-open');
                    } else {
                        document.body.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('novacrm_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed'));
                    }
                });
            }

            if (sidebarCloseBtn) {
                sidebarCloseBtn.addEventListener('click', () => {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            }

            if (localStorage.getItem('novacrm_sidebar_collapsed') === 'true' && window.innerWidth >= 768) {
                document.body.classList.add('sidebar-collapsed');
            }

            // 3. Global Search Keyboard Shortcut (Ctrl+K)
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.getElementById('global-search-input');
                    if (searchInput) searchInput.focus();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
