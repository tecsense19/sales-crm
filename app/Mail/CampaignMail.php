<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $body, $recipient)
    {
        $this->subject = $subject;
        $this->body = $this->parseBody($body, $recipient);
        $this->recipient = $recipient;
    }

    protected function parseBody($body, $recipient)
    {
        $placeholders = [
            '{{name}}' => $recipient['name'] ?? 'Client',
            '{{email}}' => $recipient['email'] ?? '',
            '{{location}}' => $recipient['location'] ?? 'N/A',
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $body);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            with: [
                'body' => $this->body,
            ],
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
