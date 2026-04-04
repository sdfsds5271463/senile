<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestAlert extends Notification
{
    use Queueable;

    public String $msg;
    public function __construct(String $msg)
    {
        $this->msg = $msg;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('這行是 subject')
                    ->line('這行是 line 自訂 msg 是 ' . $this->msg)
                    ->action('這行是 action 帶 url ', url('https://twvixstock.qzz.io/'))
                    ->line('這行是 line');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'var1' => '參數1',
            'var2' => '參數2 自訂 msg 是 ' . $this->msg,
            'message' => "隨意定義通知參數",
        ];
    }
}
