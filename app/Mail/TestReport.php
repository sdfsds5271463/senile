<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【test】這是一封測試信件',
            from: 'hello@demomailtrap.co',  //虛構信箱，這裡會覆蓋 .env 的設定
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.test.report',
            with: [
                'url' => 'https://twvixstock.qzz.io/',
                'msg' => "信件內容打在這邊阿", 
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
