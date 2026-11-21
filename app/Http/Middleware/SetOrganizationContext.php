<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetOrganizationContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (! session()->has('active_organization_id')) {
                $activeOrgId = $user->organization_id;

                if (! $activeOrgId && $user->organizations()->exists()) {
                    $activeOrgId = $user->organizations()->first()->id;
                }

                session(['active_organization_id' => $activeOrgId]);
            }
        }

        return $next($request);
    }
}
