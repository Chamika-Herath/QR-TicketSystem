<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TicketMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public bool $isResend;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, bool $isResend = false)
    {
        $this->ticket = $ticket;
        $this->isResend = $isResend;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $prefix = $this->isResend ? "[RESEND] " : "";
        return new Envelope(
            subject: $prefix . "Your Ticket for " . $this->ticket->attendee->event->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket',
            with: [
                'name' => $this->ticket->attendee->name,
                'eventName' => $this->ticket->attendee->event->name,
                'startsAt' => $this->ticket->attendee->event->starts_at->format('M d, Y h:i A'),
                'venue' => $this->ticket->attendee->event->venue,
                'token' => $this->ticket->token,
                'ticketType' => $this->ticket->ticket_type,
                'seatNumber' => $this->ticket->seat_number,
                'isResend' => $this->isResend,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $path = 'public/' . str_replace('storage/', '', $this->ticket->ticket_image_path);
        
        if (Storage::exists($path)) {
            return [
                Attachment::fromPath(Storage::path($path))
                    ->as('ticket-' . $this->ticket->token . '.png')
                    ->withMime('image/png'),
            ];
        }

        return [];
    }
}
