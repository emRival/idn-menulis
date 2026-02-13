<?php

namespace App\Models;

use App\Traits\EncryptsAttributes;
use App\Traits\HasTwoFactorAuthentication;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use App\Services\EncryptionService;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasTwoFactorAuthentication, EncryptsAttributes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'avatar',
        'cover_image',
        'bio',
        'full_name',
        'school_name',
        'class',
        'is_active',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'phone',
        'last_login_at',
        'last_login_ip',
        'login_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'phone',
        'last_login_ip',
    ];

    /**
     * Fields that should be encrypted in database.
     */
    protected array $encryptedFields = [
        'phone',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'login_count' => 'integer',
        ];
    }

    /**
     * Get encrypted fields for this model.
     */
    protected function getEncryptedFields(): array
    {
        return $this->encryptedFields;
    }

    /**
     * Get hashid for URL (anti-IDOR).
     */
    public function getHashIdAttribute(): string
    {
        return app(EncryptionService::class)->encodeId($this->id);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    /**
     * Get user by hashid.
     */
    public static function findByHashId(string $hashId): ?self
    {
        $id = app(EncryptionService::class)->decodeId($hashId);
        return $id ? static::find($id) : null;
    }

    /**
     * Encrypt remember token before saving.
     */
    public function setRememberTokenAttribute($value): void
    {
        if ($value) {
            $this->attributes['remember_token'] = app(EncryptionService::class)->encryptRememberToken($value);
        } else {
            $this->attributes['remember_token'] = null;
        }
    }

    /**
     * Record login activity.
     */
    public function recordLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'login_count' => ($this->login_count ?? 0) + 1,
        ]);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is guru.
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Check if user is siswa.
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Check if user has premium access.
     */
    public function hasPremiumAccess(): bool
    {
        // Admin and Guru always have premium access
        if ($this->isAdmin() || $this->isGuru()) {
            return true;
        }

        // Check for premium subscription (implement your subscription logic)
        // For now, return false for regular users
        return false;
    }

    /**
     * Get all articles written by user.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get all comments by user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all approvals done by user.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(ArticleApproval::class, 'reviewer_id');
    }

    /**
     * Get all likes by user.
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'likes')
            ->withPivot('created_at');
    }

    /**
     * Get all bookmarks by user.
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'bookmarks')
            ->withPivot('created_at');
    }

    /**
     * Get all notifications for user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all activity logs for user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
