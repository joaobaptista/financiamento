<?php

namespace App\Models;

use App\Domain\Campaign\Campaign;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CreatorPage extends Model
{
    protected $fillable = [
        'owner_user_id',
        'name',
        'slug',
        'primary_category',
        'subcategory',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'creator_page_followers', 'creator_page_id', 'follower_id')
            ->withTimestamps();
    }

    public static function ensureDefaultForUser(User $user): self
    {
        $existing = self::query()
            ->where('owner_user_id', $user->id)
            ->orderBy('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        $baseSlug = Str::slug((string) ($user->name ?: 'criador'));
        $slug = $baseSlug;

        if (self::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $user->id;
        }

        return self::create([
            'owner_user_id' => $user->id,
            'name' => (string) ($user->name ?: 'Criador'),
            'slug' => $slug,
            'primary_category' => null,
            'subcategory' => null,
        ]);
    }
}
