<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MagicLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLinkController extends Controller
{
    public function consume(Request $request, ?string $token = null)
    {
        // Aceita token via path (/m/{token}) ou query string (/auth/acesso?t=...)
        $token = $token ?? $request->query('t');

        if (!$token) {
            return redirect()->route('magic-link.invalid');
        }

        $magicLink = MagicLink::findValid($token);

        if (!$magicLink) {
            return redirect()->route('magic-link.invalid');
        }

        $user = $magicLink->tokenable;

        if (!$user) {
            return redirect()->route('magic-link.invalid');
        }

        $guard = match (true) {
            $user instanceof \App\Models\Leader => 'leader',
            $user instanceof \App\Models\Collaborator => 'collaborator',
            default => null,
        };

        if (!$guard) {
            return redirect()->route('magic-link.invalid');
        }

        $magicLink->consume(
            $request->ip(),
            $request->userAgent()
        );

        Auth::guard($guard)->login($user, remember: true);

        AuditLog::record(
            $guard, $user->id, "{$guard}.login",
            get_class($user), $user->id,
            ['purpose' => $magicLink->purpose],
            $request->ip(), $request->userAgent()
        );

        if ($guard === 'leader') {
            $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip(), 'status' => 'active']);
            return redirect()->route('leader.dashboard');
        }

        if ($guard === 'collaborator') {
            $user->update(['first_access_at' => $user->first_access_at ?? now()]);
            return redirect()->route('training.intro');
        }

        return redirect('/');
    }

    public function invalid()
    {
        return view('auth.magic-link-invalid');
    }
}
