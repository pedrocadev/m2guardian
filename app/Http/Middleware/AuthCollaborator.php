<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCollaborator
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('collaborator')->check()) {
            return redirect()->route('magic-link.invalid');
        }

        return $next($request);
    }
}
