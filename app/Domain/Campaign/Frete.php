<?php

namespace App\Domain\Campaign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Frete extends Model
{
    protected $table = 'fretes';

    protected $fillable = [
        'reward_id',
        'regiao',
        'valor',
    ];

    protected $casts = [
        'valor' => 'integer',
    ];

    // Relationships
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}
