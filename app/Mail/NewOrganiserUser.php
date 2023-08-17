<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrganiserUser extends Mailable
{
    use Queueable, SerializesModels;

    public ?string $organiserName;
    public $preferences;

    /**
     * Create a new message instance.
     */
    public function __construct(public $name, public $role, public $email, public $password, $organiser)
    {
        $this->organiserName = $organiser->name ?? null;
        $organiserLogo = $organiser->logo_url ?? null;

        $this->preferences = $organiser->preferences ?: [
            'email_bg_color' => 'transparent',
            'email_font_color' => '#000000',
            'email_qr_color' => '#000000',
            'email_logo_url' => $organiserLogo,
            'email_logo_width' => '200',
            'email_logo_height' => '100'
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@nidlp.gov.sa', $this->organiserName),
            subject: 'Welcome on-board | ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.new_organiser_user',
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
