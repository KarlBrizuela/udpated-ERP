<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDivisionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $division
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $division = null)
    {
        $user = Auth::user();

        // Super admins have access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // If no specific division is required, allow access
        if (!$division) {
            return $next($request);
        }

        // Allow "All Divisions" users to access everything
        if ($user->division === 'All Divisions') {
            return $next($request);
        }

        // Map division parameter to database values
        $divisionMap = [
            'production' => 'Production Division',
            'marketing' => 'Marketing Division',
            'admin-finance' => 'Admin & Finance Division',
        ];

        $requiredDivision = $divisionMap[$division] ?? $division;

        // Check if user has access to the required division
        // We check against the user's assigned divisions pivot table
        $hasAccess = $user->divisions()->where('division', $requiredDivision)->exists();

        // Fallback: Check user's primary division column if no multi-access records exist
        if (!$hasAccess && $user->division === $requiredDivision) {
            $hasAccess = true;
        }

        if (!$hasAccess && $user->division !== 'All Divisions') {
             // Redirect to user's main division (fallback) or first available division
             $firstDivision = $user->divisions()->first();
             $redirectDivision = $firstDivision ? $firstDivision->division : $user->division;

            // PREVENT REDIRECTION LOOP: 
            // If we are already trying to access the division we would redirect to, 
            // but we don't have access, then abort to prevent a loop.
            if ($redirectDivision === $requiredDivision) {
                // If it's their primary division, they should probably have access
                // but if we got here, they don't. Avoid looping.
                abort(403, 'Unauthorized access to ' . $requiredDivision);
            }

            $userDivisionRoute = $this->getUserDivisionRoute($redirectDivision);
            
            return redirect()->route($userDivisionRoute);
        }

        return $next($request);
    }

    /**
     * Get the dashboard route for a user's division.
     *
     * @param  string  $division
     * @return string
     */
    private function getUserDivisionRoute($division)
    {
        $routeMap = [
            'Production Division' => 'production.dashboard',
            'Marketing Division' => 'marketing.dashboard',
            'Admin & Finance Division' => 'admin-finance.dashboard',
        ];

        return $routeMap[$division] ?? 'dashboard';
    }
}
