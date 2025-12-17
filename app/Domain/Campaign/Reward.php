<?php

namespace App\Domain\Campaign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Pledge\Pledge;

class Reward extends Model
{
    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'min_amount',
        'quantity',
        'remaining',
    ];

    protected $casts = [
        'min_amount' => 'integer',
        'quantity' => 'integer',
        'remaining' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reward) {
            if ($reward->quantity !== null && $reward->remaining === null) {
                $reward->remaining = $reward->quantity;
            }
        });
    }

    // Relationships
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    // Business Logic
    public function isAvailable(): bool
    {
        // Se quantity é null, é ilimitado
        if ($this->quantity === null) {
            return true;
        }

        return $this->remaining > 0;
    }

    public function decrementRemaining(): bool
    {
        if ($this->quantity === null) {
            return true; // Ilimitado, não precisa decrementar
        }

        if ($this->remaining <= 0) {
            return false;
        }

        $this->remaining--;
        return $this->save();
    }

    public function getFormattedMinAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->min_amount / 100, 2, ',', '.');
    }
}
