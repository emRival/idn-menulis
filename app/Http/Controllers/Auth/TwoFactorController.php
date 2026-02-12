<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    /**
     * Show 2FA settings page.
     */
    public function index()
    {
        $user = Auth::user();

        return view('auth.two-factor', [
            'enabled' => $user->hasTwoFactorEnabled(),
            'recoveryCodes' => $user->hasTwoFactorEnabled() ? $user->getRecoveryCodes() : [],
        ]);
    }

    /**
     * Enable 2FA - Step 1: Generate secret.
     */
    public function enable(Request $request)
    {
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return back()->with('error', 'Two-Factor Authentication sudah aktif.');
        }

        $setup = $user->enableTwoFactorAuth();

        // Generate QR code URL
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' .
            urlencode($setup['qr_code_url']);

        return view('auth.two-factor-confirm', [
            'secret' => $setup['secret'],
            'qrCodeUrl' => $qrCodeUrl,
            'recoveryCodes' => $setup['recovery_codes'],
        ]);
    }

    /**
     * Confirm 2FA with code.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if ($user->confirmTwoFactorAuth($request->code)) {
            SecurityLog::log(
                SecurityLog::EVENT_2FA_ENABLED,
                $user->id,
                $request->ip(),
                $request->userAgent(),
                [],
                'info'
            );

            return redirect()->route('profile.security')
                ->with('success', 'Two-Factor Authentication berhasil diaktifkan!');
        }

        return back()->withErrors([
            'code' => 'Kode tidak valid. Pastikan kode dari aplikasi authenticator Anda.',
        ]);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->disableTwoFactorAuth();

        SecurityLog::log(
            SecurityLog::EVENT_2FA_DISABLED,
            $user->id,
            $request->ip(),
            $request->userAgent(),
            [],
            'warning'
        );

        return back()->with('success', 'Two-Factor Authentication berhasil dinonaktifkan.');
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $codes = $user->regenerateRecoveryCodes();

        return back()->with([
            'success' => 'Recovery codes berhasil di-generate ulang.',
            'recoveryCodes' => $codes,
        ]);
    }

    /**
     * Show 2FA challenge page.
     */
    public function challenge()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify 2FA code during login.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $code = $request->code;

        // Try OTP code first
        if (strlen($code) === 6 && $user->validateTwoFactorCode($code)) {
            return $this->completeTwoFactorLogin($request, $user);
        }

        // Try recovery code
        if ($user->validateRecoveryCode($code)) {
            return $this->completeTwoFactorLogin($request, $user);
        }

        SecurityLog::log(
            SecurityLog::EVENT_2FA_FAILED,
            $user->id,
            $request->ip(),
            $request->userAgent(),
            ['attempted_code' => substr($code, 0, 2) . '****'],
            'warning'
        );

        throw ValidationException::withMessages([
            'code' => 'Kode tidak valid.',
        ]);
    }

    /**
     * Complete 2FA login process.
     */
    protected function completeTwoFactorLogin(Request $request, $user)
    {
        // Clear 2FA session
        session()->forget('2fa_user_id');

        // Login the user
        Auth::login($user, session('2fa_remember', false));
        session()->forget('2fa_remember');

        // Mark 2FA as verified
        $user->markTwoFactorVerified();

        // Regenerate session
        $request->session()->regenerate();

        // Log successful 2FA
        SecurityLog::log(
            SecurityLog::EVENT_LOGIN,
            $user->id,
            $request->ip(),
            $request->userAgent(),
            ['2fa_verified' => true],
            'info'
        );

        return redirect()->intended(route('dashboard'));
    }
}
