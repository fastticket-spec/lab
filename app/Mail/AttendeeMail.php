<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $content;
    public string $title;

    /**
     * Create a new message instance.
     */
    public function __construct($settings, $lang)
    {
        $this->content = $lang == 'arabic' ? ($settings->email_message_arabic ?? '<p></p>') : ($settings->email_message ?? '<p></p>');
        $this->title = $settings->email_message_title ?? 'Attendee Mail';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.attendee_mail',
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
