<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class StrongPassword implements ValidationRule
{
    protected bool $checkPwned;

    public function __construct(bool $checkPwned = false)
    {
        $this->checkPwned = $checkPwned;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $config = config('security.password');

        // Minimum length
        if (strlen($value) < ($config['min_length'] ?? 8)) {
            $fail('Password minimal :min karakter.')->translate(['min' => $config['min_length'] ?? 8]);
            return;
        }

        // Maximum length
        if (strlen($value) > ($config['max_length'] ?? 128)) {
            $fail('Password maksimal :max karakter.')->translate(['max' => $config['max_length'] ?? 128]);
            return;
        }

        // Require uppercase
        if (($config['require_uppercase'] ?? true) && !preg_match('/[A-Z]/', $value)) {
            $fail('Password harus mengandung minimal satu huruf besar.');
            return;
        }

        // Require lowercase
        if (($config['require_lowercase'] ?? true) && !preg_match('/[a-z]/', $value)) {
            $fail('Password harus mengandung minimal satu huruf kecil.');
            return;
        }

        // Require number
        if (($config['require_number'] ?? true) && !preg_match('/[0-9]/', $value)) {
            $fail('Password harus mengandung minimal satu angka.');
            return;
        }

        // Require special character
        if (($config['require_special'] ?? true) && !preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('Password harus mengandung minimal satu karakter khusus (!@#$%^&* dll).');
            return;
        }

        // Check common passwords
        $commonPasswords = [
            'password', '123456', '12345678', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey',
            '1234567890', 'password1', 'iloveyou', 'sunshine', 'princess'
        ];

        if (in_array(strtolower($value), $commonPasswords)) {
            $fail('Password terlalu umum. Silakan pilih password yang lebih unik.');
            return;
        }

        // Check if password has been pwned (optional)
        if ($this->checkPwned && config('security.password.check_pwned', false)) {
            if ($this->isPasswordPwned($value)) {
                $fail('Password ini pernah bocor dalam data breach. Silakan gunakan password lain.');
                return;
            }
        }
    }

    /**
     * Check if password has been exposed in data breaches using Have I Been Pwned API.
     */
    protected function isPasswordPwned(string $password): bool
    {
        try {
            $hash = strtoupper(sha1($password));
            $prefix = substr($hash, 0, 5);
            $suffix = substr($hash, 5);

            $response = Http::timeout(5)->get("https://api.pwnedpasswords.com/range/{$prefix}");

            if ($response->successful()) {
                $hashes = explode("\n", $response->body());
                foreach ($hashes as $line) {
                    list($hashSuffix, $count) = explode(':', trim($line));
                    if (strtoupper($hashSuffix) === $suffix) {
                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            // If API fails, allow the password
            return false;
        }

        return false;
    }
}
