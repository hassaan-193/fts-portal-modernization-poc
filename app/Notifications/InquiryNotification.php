<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Inquiry;

class InquiryNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $body;
    protected $inquiry;

    public function __construct(string $title, string $body, Inquiry $inquiry)
    {
        $this->title   = $title;
        $this->body    = $body;
        $this->inquiry = $inquiry;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'  => $this->title,
            'note'   => $this->body,
            'action' => route('inquiries.show', $this->inquiry->id),
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
