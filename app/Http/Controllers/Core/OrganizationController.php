<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations.
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->is_super_admin) {
            $organizations = Organization::withCount(['users', 'leads', 'deals'])->latest()->get();
        } else {
            $organizations = $user->organizations()->withCount(['users'])->get();
        }

        return view('settings.organizations.index', compact('organizations'));
    }

    /**
     * Switch user's active tenant organization.
     */
    public function switch(Request $request, Organization $organization): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->is_super_admin && ! $user->organizations->contains('id', $organization->id) && $user->organization_id !== $organization->id) {
            abort(403, 'You are not a member of this organization.');
        }

        $user->switchOrganization($organization->id);

        AuditLog::log('switch_organization', $organization);

        return redirect()->route('dashboard')->with('success', 'Switched active workspace to '.$organization->name);
    }

    /**
     * Create a new organization.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'string'],
            'domain' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(4);

        $org = Organization::create($validated);
        $user = Auth::user();

        $org->members()->attach($user->id, ['is_default' => false]);
        $user->switchOrganization($org->id);

        AuditLog::log('create_organization', $org);

        return redirect()->route('dashboard')->with('success', 'Organization '.$org->name.' created successfully.');
    }
}
