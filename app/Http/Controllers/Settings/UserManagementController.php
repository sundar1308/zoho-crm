<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display listing of organization users.
     */
    public function index(): View
    {
        $orgId = session('active_organization_id') ?? Auth::user()->organization_id;
        $users = User::where('organization_id', $orgId)
            ->with(['roles', 'teams'])
            ->latest()
            ->paginate(15);

        $roles = Role::where(function ($q) use ($orgId) {
            $q->whereNull('organization_id')->orWhere('organization_id', $orgId);
        })->get();

        return view('settings.users.index', compact('users', 'roles'));
    }

    /**
     * Store a new user / invitation.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = session('active_organization_id') ?? Auth::user()->organization_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'organization_id' => $orgId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'title' => $validated['title'] ?? null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $user->roles()->attach($validated['role_id']);

        if ($org = Organization::find($orgId)) {
            $org->members()->attach($user->id, ['is_default' => true]);
        }

        AuditLog::log('create_user', $user, null, $user->only(['name', 'email', 'title']));

        return back()->with('success', "User {$user->name} created successfully.");
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', Password::defaults()],
        ]);

        $oldValues = $user->only(['name', 'email', 'phone', 'title']);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'title' => $validated['title'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->roles()->sync([$validated['role_id']]);

        AuditLog::log('update_user', $user, $oldValues, $user->only(['name', 'email', 'phone', 'title']));

        return back()->with('success', "User {$user->name} updated successfully.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        AuditLog::log("user_{$status}", $user);

        return back()->with('success', "User {$user->name} has been {$status}.");
    }
}
