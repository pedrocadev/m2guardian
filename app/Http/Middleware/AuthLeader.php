<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthLeader
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('leader')->check()) {
            return redirect()->route('leader.login');
        }

        $leader = Auth::guard('leader')->user();

        if ($leader->must_change_password) {
            $allowedRoutes = ['leader.password.change', 'leader.password.update', 'leader.logout'];
            if (!in_array($request->route()?->getName(), $allowedRoutes, true)) {
                return redirect()->route('leader.password.change');
            }
        }

        return $next($request);
    }
}
