<?php

namespace App\Notifications;

use App\Domain\Campaign\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignSuccessful extends Notification
{
    use Queueable;

    public function __construct(private readonly Campaign $campaign)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $recipientName = (string) ($notifiable->name ?? '');

        $campaignUrl = route('campaigns.show', ['slug' => $this->campaign->slug]);

        return (new MailMessage)
            ->subject('Campanha financiada: ' . (string) $this->campaign->title)
            ->markdown('emails.campaigns.successful', [
                'recipientName' => $recipientName,
                'campaignTitle' => (string) $this->campaign->title,
                'campaignUrl' => $campaignUrl,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'campaign_successful',
            'campaign_id' => $this->campaign->id,
            'campaign_slug' => (string) $this->campaign->slug,
        ];
    }
}
