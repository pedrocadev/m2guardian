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

        return $next($request);
    }
}
