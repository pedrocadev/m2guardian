<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return $next($request);
        }

        // Skip 2FA routes themselves
        if ($request->routeIs('admin.two-factor.*')) {
            return $next($request);
        }

        // If 2FA is confirmed (set up) and session not yet verified this session
        if ($admin->two_factor_confirmed_at && !$request->session()->get('admin_2fa_verified')) {
            return redirect()->route('admin.two-factor.challenge');
        }

        return $next($request);
    }
}
