<?php

namespace App\Jobs;

use App\Domain\Pledge\Pledge;
use App\Enums\PledgeStatus;
use App\Notifications\PledgeCheckoutIncomplete;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCheckoutIncompleteRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $delayMinutes = (int) config('payments.checkout_incomplete.delay_minutes', 60);
        if ($delayMinutes < 1) {
            return;
        }

        $cutoff = now()->subMinutes($delayMinutes);

        Pledge::query()
            ->where('status', PledgeStatus::Pending->value)
            ->where('payment_method', 'pix')
            ->whereNotNull('provider_payment_id')
            ->whereNull('checkout_incomplete_reminded_at')
            ->where('created_at', '<=', $cutoff)
            ->with(['user', 'campaign'])
            ->orderBy('id')
            ->lazyById(200)
            ->each(function (Pledge $pledge): void {
                $user = $pledge->user;
                $campaign = $pledge->campaign;

                if (!$user || !$campaign) {
                    return;
                }

                $projectTitle = (string) $campaign->title;
                $projectUrl = route('campaigns.show', ['slug' => $campaign->slug]);

                $user->notify(new PledgeCheckoutIncomplete(
                    projectTitle: $projectTitle,
                    projectUrl: $projectUrl,
                ));

                $pledge->checkout_incomplete_reminded_at = now();
                $pledge->save();
            });
    }
}
