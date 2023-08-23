<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $content;
    public string $title;
    public ?string $organiserName;
    public ?string $firstName;
    public $preferences;

    /**
     * Create a new message instance.
     */
    public function __construct($settings, $lang, $organiser, $firstName)
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

        $this->content = $lang == 'arabic' ? ($settings->email_message_arabic ?? '<p></p>') : ($settings->email_message ?? '<p></p>');
        $this->title = $settings->email_message_title ?? 'Attendee Mail';
        $this->firstName = $firstName;



        $this->content = str_replace(
            '%first_name%',
            $firstName,
            $this->content
        );

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@achieveone.sa', $this->organiserName),
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
