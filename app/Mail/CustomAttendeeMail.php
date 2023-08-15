<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomAttendeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public ?string $organiserName;
    public $preferences;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $data, $organiser)
    {
        $this->organiserName = $organiser->name ?? null;
        $organiserLogo = $organiser->logo_url ?? null;

        $this->preferences = $organiser->preferences ?: [
            'email_bg_color' => 'transparent',
            'email_font_color' => '#000000',
            'email_qr_color' => '#000000',
            'email_logo_url' => $organiserLogo
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('register@accreditation.achieveone.sa', $this->organiserName),
            subject: $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.custom_attendee_mail',
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
