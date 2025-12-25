<?php

namespace App\Domain\Campaign;

use App\Enums\CampaignStatus;
use App\Models\CreatorPage;
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
        'creator_page_id',
        'title',
        'slug',
        'description',
        'niche',
        'goal_amount',
        'pledged_amount',
        'goal_reached_notified_at',
        'starts_at',
        'ends_at',
        'ending_soon_notified_at',
        'finished_notified_at',
        'status',
        'cover_image_path',
        'cover_image_webp_path',
    ];

    protected $casts = [
        'goal_amount' => 'integer',
        'pledged_amount' => 'integer',
        'goal_reached_notified_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'ending_soon_notified_at' => 'datetime',
        'finished_notified_at' => 'datetime',
        'status' => CampaignStatus::class,
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

    public function creatorPage(): BelongsTo
    {
        return $this->belongsTo(CreatorPage::class);
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
        return $query->where('status', CampaignStatus::Active->value);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', CampaignStatus::Draft->value);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', CampaignStatus::Successful->value);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', CampaignStatus::Failed->value);
    }

    // Business Logic
    public function publish(): bool
    {
        if ($this->status !== CampaignStatus::Draft) {
            return false;
        }

        $this->status = CampaignStatus::Active;

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

    public function getCoverImagePathAttribute($value): ?string
    {
        return $this->normalizePublicImagePath($value);
    }

    public function getCoverImageWebpPathAttribute($value): ?string
    {
        return $this->normalizePublicImagePath($value);
    }

    private function normalizePublicImagePath($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        // Keep absolute URLs (http, https, s3, etc.) and data URIs untouched.
        if (preg_match('#^[a-z][a-z0-9+\-.]*://#i', $value) === 1 || str_starts_with($value, 'data:')) {
            return $value;
        }

        // Root-relative paths are safe as-is.
        if (str_starts_with($value, '/')) {
            return $value;
        }

        // If user stored something like "storage/foo.jpg", fix missing leading slash.
        if (str_starts_with($value, 'storage/') || str_starts_with($value, 'img/') || str_starts_with($value, 'build/')) {
            return '/' . $value;
        }

        // Otherwise treat as a path under the public disk (served via /storage symlink).
        return '/storage/' . ltrim($value, '/');
    }
}
