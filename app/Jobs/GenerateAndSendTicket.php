<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketGeneratorService;
use App\Mail\TicketMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class GenerateAndSendTicket implements ShouldQueue
{
    use Queueable;

    protected Ticket $ticket;
    protected bool $isResend;

    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket, bool $isResend = false)
    {
        $this->ticket = $ticket;
        $this->isResend = $isResend;
    }

    /**
     * Execute the job.
     */
    public function handle(TicketGeneratorService $generatorService): void
    {
        // 1. Generate QR and Ticket Composite
        $generatorService->generate($this->ticket);

        // 2. Dispatch/Send Mailable
        Mail::to($this->ticket->attendee->email)->send(new TicketMail($this->ticket, $this->isResend));
    }
}
