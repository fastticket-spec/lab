<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
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
    public ?string $firstName;
    private bool $registration;

    /**
     * Create a new message instance.
     */
    public function __construct($settings, $surveyLink, $organiser, $firstName = 'Applicant', $registration = false, $ref = null)
    {
        $this->organiserName = $organiser->name ?? null;
        $this->organiserLogo = $organiser->logo_url ?? null;

        $content = $settings->invitation_message ?? '';
        $this->title = $settings->invitation_title ?? 'Invitation Link';
        $this->firstName = $firstName;
        if ($content) {
            $this->content = str_replace(
                '%invitation_link%',
                "<a href='$surveyLink'>$surveyLink</a>",
                $content
            );

            if ($registration) {
                $this->content = str_replace(
                    '%registration_number%',
                    $ref,
                    $this->content
                );
            }

        } else {
            $this->content = "<span>Your survey link is <strong><a href='$surveyLink'>$surveyLink</a></strong></span>";

            if ($registration) {
                $this->content = "$this->content <br> <span>Your registration number is: $ref</span>";
            }
        }
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('register@accreditation.achieveone.sa', $this->organiserName),
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
