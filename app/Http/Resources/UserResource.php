<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\EncryptionService;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $encryption = app(EncryptionService::class);
        $isOwner = $request->user() && $request->user()->id === $this->id;
        $isAdmin = $request->user() && $request->user()->isAdmin();

        return [
            'id' => $encryption->encodeId($this->id), // Hashid instead of raw ID
            'username' => $this->username,
            'full_name' => $this->full_name,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : $this->getDefaultAvatar(),
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'bio' => $this->bio,
            'role' => $this->role,
            'school_name' => $this->school_name,
            'class' => $this->class,

            // Private fields - only for owner or admin
            'email' => $this->when($isOwner || $isAdmin, $this->email),
            'phone' => $this->when($isOwner || $isAdmin, fn() => $this->getEncryptedAttribute('phone')),
            'is_active' => $this->when($isAdmin, $this->is_active),
            'email_verified_at' => $this->when($isOwner || $isAdmin, $this->email_verified_at?->toIso8601String()),

            // Security info - only for owner
            'two_factor_enabled' => $this->when($isOwner, fn() => !is_null($this->two_factor_confirmed_at)),
            'last_login_at' => $this->when($isOwner, $this->last_login_at?->toIso8601String()),

            // Statistics
            'articles_count' => $this->whenCounted('articles'),
            'followers_count' => $this->when(isset($this->followers_count), $this->followers_count ?? 0),
            'following_count' => $this->when(isset($this->following_count), $this->following_count ?? 0),

            // Timestamps
            'created_at' => $this->created_at->toIso8601String(),

            // URLs
            'profile_url' => route('profile.public', $this->username),
        ];
    }

    /**
     * Get default avatar URL.
     */
    protected function getDefaultAvatar(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=14b8a6&color=fff';
    }
}
