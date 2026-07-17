<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\WashBalanceService;
use Illuminate\Support\Facades\Auth;

class RequireWashBalance
{
    protected WashBalanceService $balanceService;

    public function __construct(WashBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Do not block GET requests so that Blade components can render the overlay
        if ($request->isMethod('GET') || $request->isMethod('HEAD') || $request->isMethod('OPTIONS')) {
            return $next($request);
        }

        $user = Auth::user();
        if (! $user) {
            // Let authentication middleware decide what to do.
            return $next($request);
        }

        if ($this->balanceService->hasRequiredBalance($user)) {
            return $next($request);
        }

        // API request – respond with JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Upgrade Required: At least ' . WashBalanceService::MIN_BALANCE . ' WASH tokens needed.',
            ], Response::HTTP_FORBIDDEN);
        }

        // Web request (POST/PUT/DELETE) – redirect to an upgrade/info page
        return redirect()
            ->route('wash.upgrade')
            ->with('error', 'You need at least ' . WashBalanceService::MIN_BALANCE . ' WASH tokens to perform this action.');
    }
}
?>
