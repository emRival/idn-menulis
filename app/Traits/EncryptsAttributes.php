<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

trait EncryptsAttributes
{
    /**
     * Get encrypted attribute.
     */
    public function getEncryptedAttribute(string $key): ?string
    {
        $value = $this->attributes[$key] ?? null;

        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            Log::warning("Failed to decrypt attribute {$key} for model " . get_class($this));
            return $value; // Return as-is if not encrypted
        }
    }

    /**
     * Set encrypted attribute.
     */
    public function setEncryptedAttribute(string $key, ?string $value): void
    {
        if ($value === null) {
            $this->attributes[$key] = null;
            return;
        }

        $this->attributes[$key] = Crypt::encryptString($value);
    }

    /**
     * Define which attributes should be encrypted.
     * Override in your model.
     */
    protected function getEncryptedFields(): array
    {
        return config('security.encryption.encrypt_fields', []);
    }

    /**
     * Boot the trait.
     */
    public static function bootEncryptsAttributes(): void
    {
        static::saving(function ($model) {
            $encryptedFields = $model->getEncryptedFields();

            foreach ($encryptedFields as $field) {
                if (isset($model->attributes[$field]) && $model->isDirty($field)) {
                    $value = $model->attributes[$field];

                    // Only encrypt if not already encrypted
                    if (!$model->isEncrypted($value)) {
                        $model->attributes[$field] = Crypt::encryptString($value);
                    }
                }
            }
        });
    }

    /**
     * Check if value is already encrypted.
     */
    protected function isEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
