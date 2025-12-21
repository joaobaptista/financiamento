<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'primary_category',
        'subcategory',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
