<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $content;
    public string  $title;
    public ?string $organiserName;
    public ?string $organiserLogo;

    /**
     * Create a new message instance.
     */
    public function __construct($settings, $surveyLink, $organiser)
    {
        $this->organiserName = $organiser->name ?? null;
        $this->organiserLogo = $organiser->logo_url ?? null;

        $content = $settings->invitation_message ?? '';
        $this->title = $settings->invitation_title ?? 'Invitation Link';
        if ($content) {
            $this->content = str_replace(
                '<strong>%invitation_link%</strong>',
                "<strong><a href='$surveyLink'>$surveyLink</a></strong>",
                $content
            );
        } else {
            $this->content = "<span>Your survey link is <strong><a href='$surveyLink'>$surveyLink</a></strong></span>";
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->organiserName,
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.invitation_mail'
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
