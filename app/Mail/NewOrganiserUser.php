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
    public ?string $organiserLogo;

    /**
     * Create a new message instance.
     */
    public function __construct(public $name, public $role, public $email, public $password, $organiser)
    {
        $this->organiserName = $organiser->name ?? null;
        $this->organiserLogo = $organiser->logo_url ?? null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('register@accreditation.achieveone.sa', $this->organiserName),
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
