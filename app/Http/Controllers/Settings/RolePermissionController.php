<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    /**
     * Display listing of roles and permission matrix.
     */
    public function index(): View
    {
        $orgId = session('active_organization_id') ?? Auth::user()->organization_id;

        $roles = Role::where(function ($q) use ($orgId) {
            $q->whereNull('organization_id')->orWhere('organization_id', $orgId);
        })->with('permissions')->get();

        $permissionsByModule = Permission::all()->groupBy('module');

        return view('settings.roles.index', compact('roles', 'permissionsByModule'));
    }

    /**
     * Store new custom role.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = session('active_organization_id') ?? Auth::user()->organization_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'organization_id' => $orgId,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'_'.Str::random(4),
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        if (! empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        AuditLog::log('create_role', $role);

        return back()->with('success', "Role {$role->name} created successfully.");
    }

    /**
     * Update role permissions.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        AuditLog::log('update_role_permissions', $role);

        return back()->with('success', "Permissions for {$role->name} updated successfully.");
    }
}
