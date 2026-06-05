<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->registerRateLimiters();
        $this->registerLoginListeners();
    }

    private function registerRateLimiters(): void
    {
        // Magic link: 10 attempts per IP per minute
        RateLimiter::for('magic-link', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Admin login: 5 attempts per IP per minute
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Invite store: 20 per minute per leader
        RateLimiter::for('invite', function (Request $request) {
            return Limit::perMinute(20)->by(
                optional($request->user('leader'))->id ?? $request->ip()
            );
        });
    }

    private function registerLoginListeners(): void
    {
        // Track failed admin login attempts → brute-force lockout
        Event::listen(Failed::class, function (Failed $event) {
            if ($event->guard !== 'admin' || !$event->credentials) {
                return;
            }

            $email = $event->credentials['email'] ?? null;
            if (!$email) return;

            $admin = Admin::where('email', $email)->first();
            if (!$admin) return;

            $attempts = $admin->failed_attempts + 1;
            $data = ['failed_attempts' => $attempts];

            if ($attempts >= 5) {
                $data['locked_until'] = now()->addMinutes(15);
                AuditLog::record('system', null, 'admin.login.locked', 'admin', $admin->id, [
                    'attempts' => $attempts, 'email' => $email,
                ]);
            }

            $admin->update($data);
        });

        // Track successful admin login → register last_login_at + reset brute-force counters
        Event::listen(Login::class, function (Login $event) {
            if ($event->guard !== 'admin' || !($event->user instanceof Admin)) {
                return;
            }

            $event->user->forceFill([
                'last_login_at'    => now(),
                'last_login_ip'    => request()->ip(),
                'failed_attempts'  => 0,
                'locked_until'     => null,
            ])->save();
        });
    }
}
