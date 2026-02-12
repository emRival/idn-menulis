<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Hashids\Hashids;

class EncryptionService
{
    protected Hashids $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(
            config('app.key'),
            10, // Minimum length
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        );
    }

    /**
     * Encrypt a string value
     */
    public function encrypt(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::encryptString($value);
        } catch (\Exception $e) {
            Log::error('Encryption failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to encrypt data');
        }
    }

    /**
     * Decrypt a string value
     */
    public function decrypt(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning('Decryption failed, returning original value');
            return $value; // Return as-is if not encrypted
        }
    }

    /**
     * Encode an ID to hashid (for URLs)
     */
    public function encodeId(int $id): string
    {
        return $this->hashids->encode($id);
    }

    /**
     * Decode a hashid back to ID
     */
    public function decodeId(string $hash): ?int
    {
        $decoded = $this->hashids->decode($hash);
        return $decoded[0] ?? null;
    }

    /**
     * Generate secure random token
     */
    public function generateSecureToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Hash password with validation
     */
    public function hashPassword(string $password): string
    {
        $this->validatePasswordStrength($password);
        return Hash::make($password);
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength(string $password): bool
    {
        $config = config('security.password');

        if (strlen($password) < ($config['min_length'] ?? 8)) {
            throw new \InvalidArgumentException('Password must be at least ' . ($config['min_length'] ?? 8) . ' characters');
        }

        if (($config['require_uppercase'] ?? true) && !preg_match('/[A-Z]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one uppercase letter');
        }

        if (($config['require_lowercase'] ?? true) && !preg_match('/[a-z]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one lowercase letter');
        }

        if (($config['require_number'] ?? true) && !preg_match('/[0-9]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one number');
        }

        if (($config['require_special'] ?? true) && !preg_match('/[^A-Za-z0-9]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one special character');
        }

        return true;
    }

    /**
     * Encrypt file contents
     */
    public function encryptFile(string $contents): string
    {
        return Crypt::encrypt($contents);
    }

    /**
     * Decrypt file contents
     */
    public function decryptFile(string $encryptedContents): string
    {
        return Crypt::decrypt($encryptedContents);
    }

    /**
     * Generate signed URL for private content
     */
    public function generateSignedHash(string $data, int $expiresInMinutes = 60): string
    {
        $expires = now()->addMinutes($expiresInMinutes)->timestamp;
        $signature = hash_hmac('sha256', $data . $expires, config('app.key'));

        return base64_encode(json_encode([
            'data' => $data,
            'expires' => $expires,
            'signature' => $signature
        ]));
    }

    /**
     * Verify signed hash
     */
    public function verifySignedHash(string $hash): ?string
    {
        try {
            $decoded = json_decode(base64_decode($hash), true);

            if (!$decoded || !isset($decoded['data'], $decoded['expires'], $decoded['signature'])) {
                return null;
            }

            if ($decoded['expires'] < now()->timestamp) {
                return null; // Expired
            }

            $expectedSignature = hash_hmac('sha256', $decoded['data'] . $decoded['expires'], config('app.key'));

            if (!hash_equals($expectedSignature, $decoded['signature'])) {
                return null; // Invalid signature
            }

            return $decoded['data'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Encrypt remember token
     */
    public function encryptRememberToken(string $token): string
    {
        return hash_hmac('sha256', $token, config('app.key'));
    }
}
