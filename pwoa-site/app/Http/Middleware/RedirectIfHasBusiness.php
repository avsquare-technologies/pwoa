<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfHasBusiness
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasBusiness()) {
            // Only apply block to CREATE flows (e.g. if request contains create query parameter)
            if ($request->routeIs('business.manage') && $request->has('create')) {
                return redirect()->route('business.manage')->with('error', 'You already have an active business listing.');
            }
        }

        return $next($request);
    }
}
