<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'profile_photo_path',
        'postal_code',
        'address_street',
        'address_number',
        'address_complement',
        'address_neighborhood',
        'address_city',
        'address_state',
        'phone',
    ];

    /**
     * Appended attributes for JSON serialization.
     *
     * @var list<string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        $path = (string) ($this->profile_photo_path ?? '');
        if ($path === '') return null;

        return Storage::disk('public')->url($path);
    }

    // Relationships
    public function campaigns()
    {
        return $this->hasMany(\App\Domain\Campaign\Campaign::class);
    }

    public function pledges()
    {
        return $this->hasMany(\App\Domain\Pledge\Pledge::class);
    }

    public function creatorProfile(): HasOne
    {
        return $this->hasOne(CreatorProfile::class);
    }

    public function creatorPages(): HasMany
    {
        return $this->hasMany(CreatorPage::class, 'owner_user_id');
    }

    public function followingCreatorPages(): BelongsToMany
    {
        return $this->belongsToMany(CreatorPage::class, 'creator_page_followers', 'follower_id', 'creator_page_id')
            ->withTimestamps();
    }

    public function supportedCreators(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'creator_supporters',
            'supporter_id',
            'creator_id'
        )->withTimestamps();
    }

    public function supporters(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'creator_supporters',
            'creator_id',
            'supporter_id'
        )->withTimestamps();
    }
}
