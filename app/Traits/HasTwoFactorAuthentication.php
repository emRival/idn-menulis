<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

trait HasTwoFactorAuthentication
{
    /**
     * Enable two-factor authentication for the user.
     */
    public function enableTwoFactorAuth(): array
    {
        $google2fa = new Google2FA();

        // Generate secret key
        $secret = $google2fa->generateSecretKey();

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        // Store encrypted
        $this->two_factor_secret = Crypt::encryptString($secret);
        $this->two_factor_recovery_codes = Crypt::encryptString(json_encode($recoveryCodes));
        $this->two_factor_confirmed_at = null;
        $this->save();

        return [
            'secret' => $secret,
            'qr_code_url' => $this->getTwoFactorQrCodeUrl($secret),
            'recovery_codes' => $recoveryCodes,
        ];
    }

    /**
     * Confirm two-factor authentication.
     */
    public function confirmTwoFactorAuth(string $code): bool
    {
        if (!$this->validateTwoFactorCode($code)) {
            return false;
        }

        $this->two_factor_confirmed_at = now();
        $this->save();

        return true;
    }

    /**
     * Disable two-factor authentication.
     */
    public function disableTwoFactorAuth(): void
    {
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->two_factor_confirmed_at = null;
        $this->save();
    }

    /**
     * Check if two-factor is enabled and confirmed.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) &&
               !empty($this->two_factor_confirmed_at);
    }

    /**
     * Validate two-factor code.
     */
    public function validateTwoFactorCode(string $code): bool
    {
        if (!$this->two_factor_secret) {
            return false;
        }

        try {
            $secret = Crypt::decryptString($this->two_factor_secret);
            $google2fa = new Google2FA();

            return $google2fa->verifyKey($secret, $code, config('security.two_factor.window', 1));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate recovery code.
     */
    public function validateRecoveryCode(string $code): bool
    {
        if (!$this->two_factor_recovery_codes) {
            return false;
        }

        try {
            $codes = json_decode(
                Crypt::decryptString($this->two_factor_recovery_codes),
                true
            );

            $code = strtoupper(trim($code));

            if (in_array($code, $codes)) {
                // Remove used code
                $codes = array_values(array_filter($codes, fn($c) => $c !== $code));
                $this->two_factor_recovery_codes = Crypt::encryptString(json_encode($codes));
                $this->save();

                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Get QR code URL for authenticator app.
     */
    public function getTwoFactorQrCodeUrl(string $secret): string
    {
        $google2fa = new Google2FA();

        $appName = config('security.two_factor.issuer', config('app.name'));

        return $google2fa->getQRCodeUrl(
            $appName,
            $this->email,
            $secret
        );
    }

    /**
     * Get decrypted recovery codes.
     */
    public function getRecoveryCodes(): array
    {
        if (!$this->two_factor_recovery_codes) {
            return [];
        }

        try {
            return json_decode(
                Crypt::decryptString($this->two_factor_recovery_codes),
                true
            );
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(): array
    {
        $codes = $this->generateRecoveryCodes();

        $this->two_factor_recovery_codes = Crypt::encryptString(json_encode($codes));
        $this->save();

        return $codes;
    }

    /**
     * Generate recovery codes.
     */
    protected function generateRecoveryCodes(): array
    {
        $count = config('security.two_factor.recovery_codes', 8);
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }

        return $codes;
    }

    /**
     * Check if 2FA is required for this session.
     */
    public function requiresTwoFactorChallenge(): bool
    {
        if (!$this->hasTwoFactorEnabled()) {
            return false;
        }

        // Check if already verified in this session
        $sessionKey = "2fa_verified:{$this->id}";

        return !Cache::has($sessionKey);
    }

    /**
     * Mark 2FA as verified for this session.
     */
    public function markTwoFactorVerified(): void
    {
        $sessionKey = "2fa_verified:{$this->id}";

        // Valid for 12 hours
        Cache::put($sessionKey, true, now()->addHours(12));
    }
}
