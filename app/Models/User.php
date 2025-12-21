<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    // Relationships
    public function campaigns()
    {
        return $this->hasMany(\App\Domain\Campaign\Campaign::class);
    }

    public function pledges()
    {
        return $this->hasMany(\App\Domain\Pledge\Pledge::class);
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
