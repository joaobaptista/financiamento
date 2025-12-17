<?php

namespace App\Domain\Pledge;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pledge extends Model
{
    protected $fillable = [
        'campaign_id',
        'user_id',
        'reward_id',
        'amount',
        'status',
        'provider',
        'provider_payment_id',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    // Business Logic
    public function markAsPaid(?string $paymentId = null): bool
    {
        $this->status = 'paid';
        $this->paid_at = now();

        if ($paymentId) {
            $this->provider_payment_id = $paymentId;
        }

        return $this->save();
    }

    public function markAsRefunded(): bool
    {
        $this->status = 'refunded';
        return $this->save();
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount / 100, 2, ',', '.');
    }
}
