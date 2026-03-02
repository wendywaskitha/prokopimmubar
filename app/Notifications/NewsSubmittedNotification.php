<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsSubmittedNotification extends Notification
{
    use Queueable;

    protected $news;

    /**
     * Create a new notification instance.
     */
    public function __construct(News $news)
    {
        $this->news = $news;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'news_id' => $this->news->id,
            'news_title' => $this->news->title,
            'author_name' => $this->news->author->name,
            'author_role' => $this->getAuthorRole(),
            'message' => $this->getMessage(),
            'created_at' => now(),
        ];
    }

    /**
     * Get the author's role
     */
    private function getAuthorRole(): string
    {
        $author = $this->news->author;
        
        if ($author->isPenulis()) {
            return 'Penulis';
        } elseif ($author->isKontributor()) {
            return 'Kontributor';
        }
        
        return 'User';
    }

    /**
     * Get the notification message
     */
    private function getMessage(): string
    {
        $authorRole = $this->getAuthorRole();
        return "Berita baru telah diajukan oleh {$authorRole} \"{$this->news->author->name}\" dengan judul \"{$this->news->title}\".";
    }
}