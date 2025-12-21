<?php

namespace App\Domain\Pledge;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Enums\PledgeStatus;
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
        'status' => PledgeStatus::class,
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
        return $query->where('status', PledgeStatus::Paid->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', PledgeStatus::Pending->value);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', PledgeStatus::Refunded->value);
    }

    // Business Logic
    public function markAsPaid(?string $paymentId = null): bool
    {
        $this->status = PledgeStatus::Paid;
        $this->paid_at = now();

        if ($paymentId) {
            $this->provider_payment_id = $paymentId;
        }

        return $this->save();
    }

    public function markAsRefunded(): bool
    {
        $this->status = PledgeStatus::Refunded;
        return $this->save();
    }

    public function markAsCanceled(): bool
    {
        if ($this->status === PledgeStatus::Paid) {
            return false;
        }

        $this->status = PledgeStatus::Canceled;
        return $this->save();
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount / 100, 2, ',', '.');
    }
}
