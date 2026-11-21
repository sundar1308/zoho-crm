<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show registration form.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle user and organization registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $org = Organization::create([
                'name' => $validated['organization_name'],
                'slug' => Str::slug($validated['organization_name']).'-'.Str::random(5),
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            $adminRole = Role::where('slug', 'admin')->first();

            $user = User::create([
                'organization_id' => $org->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]);

            $org->members()->attach($user->id, ['is_default' => true]);

            if ($adminRole) {
                $user->roles()->attach($adminRole->id);
            }

            return $user;
        });

        Auth::login($user);
        session(['active_organization_id' => $user->organization_id]);

        AuditLog::log('register', $user);

        return redirect()->route('dashboard')->with('success', 'Your organization and account have been created successfully!');
    }
}
