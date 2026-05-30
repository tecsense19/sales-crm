<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class TeamFollowUpReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $overdue;

    /**
     * Create a new message instance.
     *
     * @param Collection $overdue
     */
    public function __construct(Collection $overdue)
    {
        $this->overdue = $overdue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Your Overdue Client Follow-ups - ' . now()->format('M d, Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.team_followup_reminder',
            with: [
                'overdue' => $this->overdue,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
