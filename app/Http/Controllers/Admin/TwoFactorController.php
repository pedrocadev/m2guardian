<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /** Show QR code setup page */
    public function setup(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin->two_factor_confirmed_at) {
            return redirect('/admin')->with('info', '2FA já está ativado.');
        }

        if (!$admin->two_factor_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $admin->update(['two_factor_secret' => $secret]);
        }

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $admin->email,
            $admin->two_factor_secret
        );

        return view('admin.two-factor.setup', compact('qrCodeUrl', 'admin'));
    }

    /** Confirm 2FA setup by verifying first OTP */
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|string|min:6|max:6']);

        $admin = Auth::guard('admin')->user();

        $valid = $this->google2fa->verifyKey(
            $admin->two_factor_secret,
            $request->code
        );

        if (!$valid) {
            return back()->withErrors(['code' => 'Código inválido. Verifique seu app autenticador.']);
        }

        $codes = collect(range(1, 8))->map(fn() => strtoupper(substr(bin2hex(random_bytes(5)), 0, 10)))->toArray();

        $admin->update([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => json_encode($codes),
        ]);

        $request->session()->put('admin_2fa_verified', true);

        return redirect('/admin')->with('success', '2FA ativado com sucesso!');
    }

    /** Show 2FA challenge (after login) */
    public function challenge(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        return view('admin.two-factor.challenge');
    }

    /** Verify 2FA challenge */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $admin = Auth::guard('admin')->user();

        // Check if it's a recovery code
        $recoveryCodes = json_decode($admin->two_factor_recovery_codes ?? '[]', true);
        $normalizedCode = strtoupper(trim($request->code));

        if (in_array($normalizedCode, $recoveryCodes)) {
            $updated = array_filter($recoveryCodes, fn($c) => $c !== $normalizedCode);
            $admin->update(['two_factor_recovery_codes' => json_encode(array_values($updated))]);
            $request->session()->put('admin_2fa_verified', true);
            return redirect()->intended('/admin');
        }

        $valid = $this->google2fa->verifyKey(
            $admin->two_factor_secret,
            $request->code
        );

        if (!$valid) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        $request->session()->put('admin_2fa_verified', true);

        return redirect()->intended('/admin');
    }

    /** Disable 2FA */
    public function disable(Request $request)
    {
        $request->validate(['code' => 'required|string|min:6|max:6']);

        $admin = Auth::guard('admin')->user();

        $valid = $this->google2fa->verifyKey($admin->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Código inválido.']);
        }

        $admin->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);

        $request->session()->forget('admin_2fa_verified');

        return redirect('/admin')->with('success', '2FA desativado.');
    }
}
