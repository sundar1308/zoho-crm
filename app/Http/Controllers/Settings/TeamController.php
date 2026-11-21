<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Display listing of teams.
     */
    public function index(): View
    {
        $orgId = session('active_organization_id') ?? Auth::user()->organization_id;

        $teams = Team::with(['leader', 'members'])->latest()->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('settings.teams.index', compact('teams', 'users'));
    }

    /**
     * Store a new team.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'leader_id' => ['nullable', 'exists:users,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'leader_id' => $validated['leader_id'] ?? null,
        ]);

        if (! empty($validated['members'])) {
            $team->members()->sync($validated['members']);
        }

        AuditLog::log('create_team', $team);

        return back()->with('success', "Team {$team->name} created successfully.");
    }

    /**
     * Update an existing team.
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'leader_id' => ['nullable', 'exists:users,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'leader_id' => $validated['leader_id'] ?? null,
        ]);

        $team->members()->sync($validated['members'] ?? []);

        AuditLog::log('update_team', $team);

        return back()->with('success', "Team {$team->name} updated successfully.");
    }
}
