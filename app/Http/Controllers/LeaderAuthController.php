<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Leader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LeaderAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('leader')->check()) {
            return redirect()->route('leader.dashboard');
        }
        return view('leader.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $leader = Leader::where('email', $data['email'])->first();

        if (!$leader) {
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
        }

        if ($leader->isLocked()) {
            return back()->withErrors([
                'email' => 'Conta temporariamente bloqueada após múltiplas tentativas. Tente novamente em alguns minutos ou contate o suporte M2.',
            ])->onlyInput('email');
        }

        if ($leader->status === 'suspended') {
            return back()->withErrors(['email' => 'Acesso suspenso. Contate o suporte M2.'])->onlyInput('email');
        }

        if (!$leader->password || !Hash::check($data['password'], $leader->password)) {
            $attempts = $leader->failed_attempts + 1;
            $update = ['failed_attempts' => $attempts];
            if ($attempts >= 5) {
                $update['locked_until'] = now()->addMinutes(15);
                AuditLog::record('system', null, 'leader.login.locked', 'leader', $leader->id, [
                    'attempts' => $attempts, 'email' => $leader->email,
                ], $request->ip(), $request->userAgent());
            }
            $leader->update($update);

            return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
        }

        // Sucesso
        $leader->update([
            'failed_attempts' => 0,
            'locked_until'    => null,
            'last_login_at'   => now(),
            'last_login_ip'   => $request->ip(),
            'status'          => 'active',
        ]);

        Auth::guard('leader')->login($leader, remember: $request->boolean('remember'));
        $request->session()->regenerate();

        AuditLog::record('leader', $leader->id, 'leader.login.success',
            'leader', $leader->id, null, $request->ip(), $request->userAgent()
        );

        if ($leader->must_change_password) {
            return redirect()->route('leader.password.change');
        }

        return redirect()->intended(route('leader.dashboard'));
    }

    public function showChangePassword()
    {
        return view('leader.change-password', [
            'leader' => Auth::guard('leader')->user(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string', 'current_password:leader'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required'         => 'Informe a senha atual.',
            'current_password.current_password' => 'Senha atual incorreta.',
            'password.confirmed'                => 'A confirmação da nova senha não confere.',
            'password.min'                      => 'A nova senha precisa ter pelo menos 8 caracteres.',
            'password.different'                => 'A nova senha precisa ser diferente da atual.',
        ]);

        /** @var Leader $leader */
        $leader = Auth::guard('leader')->user();

        $leader->update([
            'password'             => $data['password'],
            'password_set_at'      => now(),
            'must_change_password' => false,
        ]);

        AuditLog::record('leader', $leader->id, 'leader.password.changed',
            'leader', $leader->id, null, $request->ip(), $request->userAgent()
        );

        return redirect()->route('leader.dashboard')->with('flash_success', 'Senha alterada com sucesso!');
    }

    public function logout(Request $request)
    {
        Auth::guard('leader')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('leader.login');
    }
}
