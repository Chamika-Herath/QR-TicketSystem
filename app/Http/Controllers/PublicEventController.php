<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\Ticket;
use App\Jobs\GenerateAndSendTicket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PublicEventController extends Controller
{
    /**
     * Display the event landing and registration page.
     */
    public function show(string $slug): Response
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return Inertia::render('Public/EventLanding', [
            'event' => $event,
        ]);
    }

    /**
     * Handle public registration for an event.
     */
    public function register(Request $request, string $slug): RedirectResponse
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Check Capacity
        $currentRegistrations = Attendee::where('event_id', $event->id)->count();
        if ($currentRegistrations >= $event->capacity) {
            return back()->withErrors(['email' => 'Sorry, this event is fully booked!']);
        }

        $allowedTypes = $event->ticket_types ?? [['name' => 'General Admission', 'capacity' => $event->capacity]];
        $allowedNames = collect($allowedTypes)->pluck('name')->toArray();

        if (!$request->has('ticket_type')) {
            $request->merge(['ticket_type' => $allowedNames[0] ?? 'General Admission']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:attendees,email,NULL,id,event_id,' . $event->id,
            'phone' => 'required|string|max:20',
            'ticket_type' => ['required', 'string', \Illuminate\Validation\Rule::in($allowedNames)],
        ], [
            'email.unique' => 'You are already registered for this event with this email address.',
        ]);

        $chosenType = $validated['ticket_type'];
        $registeredCount = Ticket::whereHas('attendee', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->where('ticket_type', $chosenType)->count();

        $categoryConfig = collect($allowedTypes)->firstWhere('name', $chosenType);
        $categoryLimit = $categoryConfig ? ($categoryConfig['capacity'] ?? 100) : 100;

        if ($registeredCount >= $categoryLimit) {
            return back()->withErrors(['ticket_type' => "Sorry, the {$chosenType} ticket category is fully booked!"]);
        }

        $seatNumber = strtoupper($chosenType) . '-' . ($registeredCount + 1);

        $attendee = Attendee::create([
            'event_id' => $event->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        do {
            $token = \Illuminate\Support\Str::random(32);
        } while (Ticket::where('token', $token)->exists());

        $ticket = Ticket::create([
            'attendee_id' => $attendee->id,
            'token' => $token,
            'ticket_type' => $chosenType,
            'price' => $categoryConfig ? ($categoryConfig['price'] ?? 0) : 0,
            'seat_number' => $seatNumber,
            'status' => 'issued',
        ]);

        // Dispatch queued background job
        GenerateAndSendTicket::dispatch($ticket);

        return back()->with('success', 'Registration successful! Your ticket is being generated and will be emailed shortly.');
    }

    /**
     * Show self-service ticket resend form.
     */
    public function showResend(): Response
    {
        $events = Event::where('status', 'published')->get(['id', 'name']);
        return Inertia::render('Public/TicketResend', [
            'events' => $events,
        ]);
    }

    /**
     * Process ticket resend.
     */
    public function resend(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'email' => 'required|email',
        ]);

        $attendee = Attendee::where('event_id', $validated['event_id'])
            ->where('email', $validated['email'])
            ->first();

        if (!$attendee || !$attendee->ticket) {
            return back()->withErrors(['email' => 'No registration found for this email address and event.']);
        }

        // Dispatch queued job with isResend = true
        GenerateAndSendTicket::dispatch($attendee->ticket, true);

        return back()->with('success', 'Ticket email has been re-queued for delivery.');
    }
}
