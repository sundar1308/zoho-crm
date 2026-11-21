<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Core\AuditLogController;
use App\Http\Controllers\Core\DashboardController;
use App\Http\Controllers\Core\OrganizationController;
use App\Http\Controllers\Settings\RolePermissionController;
use App\Http\Controllers\Settings\TeamController;
use App\Http\Controllers\Settings\UserManagementController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated Application Routes
Route::middleware(['auth', 'active.user', 'org.context'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.charts');
    Route::get('/dashboard/export', [DashboardController::class, 'exportReport'])->name('dashboard.export');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Organizations
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::post('/organizations/switch/{organization}', [OrganizationController::class, 'switch'])->name('organizations.switch');

    // System Settings & RBAC
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('users.toggle');

        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RolePermissionController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');

        Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
        Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    });

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');

    // Module Hub Screens for Navigation
    $modules = [
        'leads' => 'Leads',
        'contacts' => 'Contacts',
        'accounts' => 'Accounts',
        'deals' => 'Deals',
        'activities' => 'Activities',
        'campaigns' => 'Campaigns',
        'products' => 'Products',
        'price-books' => 'Price Books',
        'quotes' => 'Quotes',
        'sales-orders' => 'Sales Orders',
        'invoices' => 'Invoices',
        'vendors' => 'Vendors',
        'purchase-orders' => 'Purchase Orders',
        'reports' => 'Reports',
        'analytics' => 'Analytics',
        'calendar' => 'Calendar',
        'documents' => 'Documents',
        'automation' => 'Automation',
        'portals' => 'Portals',
        'integrations' => 'Integrations',
        'settings' => 'Settings',
    ];

    foreach ($modules as $slug => $title) {
        $routeName = str_replace('-', '_', $slug);
        Route::get("/{$slug}", function () use ($title, $slug) {
            return view('core.module-placeholder', [
                'moduleTitle' => $title,
                'moduleSlug' => $slug,
            ]);
        })->name("modules.{$routeName}");
    }
});
