<?php

namespace App\Data\Dashboard;

final readonly class CampaignStatsData
{
    public function __construct(
        public int $totalBackers,
        public int $totalRaised,
        public float $progress,
        public int $daysRemaining,
    ) {
    }

    /** @return array{total_backers:int,total_raised:int,progress:float,days_remaining:int} */
    public function toArray(): array
    {
        return [
            'total_backers' => $this->totalBackers,
            'total_raised' => $this->totalRaised,
            'progress' => $this->progress,
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
