<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class FollowUpReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $upcoming;
    public $overdue;

    /**
     * Create a new message instance.
     *
     * @param Collection $upcoming
     * @param Collection $overdue
     */
    public function __construct(Collection $upcoming, Collection $overdue)
    {
        $this->upcoming = $upcoming;
        $this->overdue = $overdue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Client Follow-up Digest - ' . now()->format('M d, Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.followup_reminder',
            with: [
                'upcoming' => $this->upcoming,
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
