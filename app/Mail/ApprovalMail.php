<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;
    public string $content;
    public ?string $organiserName;
    public ?string $organiserLogo;

    /**
     * Create a new message instance.
     */
    public function __construct($settings, $organiser)
    {
        $this->organiserName = $organiser->name ?? null;
        $this->organiserLogo = $organiser->logo_url ?? null;

        $this->content = $settings->approval_message ?? '<p></p>';
        $this->title = $settings->approval_message_title ?? 'Approval Mail';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->organiserName,
            subject: $this->title
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.approval_mail',
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
