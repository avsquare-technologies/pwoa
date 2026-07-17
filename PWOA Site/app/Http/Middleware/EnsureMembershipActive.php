<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMembershipActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to subscription, success, and logout routes
        $allowedRoutes = [
            'membership.subscribe_form',
            'membership.subscribe',
            'membership.success',
            'logout',
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        if (! $request->user() || ! $request->user()->isActiveMember()) {
            return redirect()->route('membership.subscribe_form')->with('warning', 'Active membership required.');
        }

        return $next($request);
    }
}
