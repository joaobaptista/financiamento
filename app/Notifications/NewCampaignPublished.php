<?php

namespace App\Notifications;

use App\Domain\Campaign\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCampaignPublished extends Notification
{
    use Queueable;

    public function __construct(private readonly Campaign $campaign)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->campaign->loadMissing(['creatorPage', 'user']);

        $creatorName = $this->campaign->creatorPage?->name
            ?? $this->campaign->user?->name
            ?? 'um criador que você apoia';
        $url = rtrim((string) config('app.url'), '/') . '/campaigns/' . $this->campaign->slug;

        return (new MailMessage)
            ->subject("Nova campanha de {$creatorName}")
            ->greeting('Olá!')
            ->line("{$creatorName} acabou de publicar uma nova campanha: {$this->campaign->title}.")
            ->action('Ver campanha', $url)
            ->line('Obrigado por apoiar criadores na plataforma.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->campaign->loadMissing(['creatorPage', 'user']);

        return [
            'type' => 'campaign_published',
            'campaign_id' => $this->campaign->id,
            'campaign_slug' => $this->campaign->slug,
            'campaign_title' => $this->campaign->title,
            'creator_page_id' => $this->campaign->creatorPage?->id,
            'creator_page_slug' => $this->campaign->creatorPage?->slug,
            'creator_page_name' => $this->campaign->creatorPage?->name,
            'creator_id' => $this->campaign->user?->id,
            'creator_name' => $this->campaign->user?->name,
        ];
    }
}
