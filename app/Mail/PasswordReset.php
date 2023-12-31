<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public function __construct($user, public $resetToken)
    {
        $this->name = $user->first_name . ' ' . $user->last_name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mails.password_reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
