<?php

namespace App\Mail;

use App\Helpers\QRCodeHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;
    public string $content;
    public ?string $organiserName;
    public $preferences;
//    public ?string $firstName;

    /**
     * Create a new message instance.
     */
    public function __construct($settings, $organiser, $attendeeRef, $firstName)
    {
        $this->organiserName = $organiser->name ?? null;
        $organiserLogo = $organiser->logo_url ?? null;

        $this->preferences = $organiser->preferences ?: [
            'email_bg_color' => 'transparent',
            'email_font_color' => '#000000',
            'email_qr_color' => '#000000',
            'email_logo_url' => $organiserLogo
        ];

        $qr = QRCodeHelper::getQRCode($attendeeRef);
        Log::info($qr);

        $this->content = $settings->approval_message ?? '<p></p>';
        $this->content = str_replace(
            '%qrcode%',
            "<img src='$qr' alt='' width='150px'>",
            $this->content
        );

        $this->content = str_replace(
            '%first_name%',
            $firstName,
            $this->content
        );

        $this->title = $settings->approval_message_title ?? 'Approval Mail';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@nidlp.gov.sa', $this->organiserName),
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
