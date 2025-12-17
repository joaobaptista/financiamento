<?php

namespace App\Domain\Campaign;

use App\Models\User;
use App\Domain\Pledge\Pledge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'goal_amount',
        'pledged_amount',
        'starts_at',
        'ends_at',
        'status',
        'cover_image_path',
    ];

    protected $casts = [
        'goal_amount' => 'integer',
        'pledged_amount' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->title);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Business Logic
    public function publish(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->status = 'active';

        if (!$this->starts_at) {
            $this->starts_at = now();
        }

        return $this->save();
    }

    public function calculateProgress(): float
    {
        if ($this->goal_amount == 0) {
            return 0;
        }

        return round(($this->pledged_amount / $this->goal_amount) * 100, 2);
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function isSuccessful(): bool
    {
        return $this->pledged_amount >= $this->goal_amount;
    }

    public function daysRemaining(): int
    {
        if (!$this->ends_at || $this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->ends_at);
    }

    public function getFormattedGoalAttribute(): string
    {
        return 'R$ ' . number_format($this->goal_amount / 100, 2, ',', '.');
    }

    public function getFormattedPledgedAttribute(): string
    {
        return 'R$ ' . number_format($this->pledged_amount / 100, 2, ',', '.');
    }
}
