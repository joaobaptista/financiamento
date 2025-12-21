<?php

namespace App\Data\Campaign;

use Illuminate\Support\Carbon;

final readonly class CreateCampaignData
{
    /** @param list<RewardData> $rewards */
    public function __construct(
        public int $userId,
        public string $title,
        public string $description,
        public int $goalAmount,
        public Carbon $endsAt,
        public ?string $coverImagePath,
        public array $rewards = [],
    ) {
    }
}
