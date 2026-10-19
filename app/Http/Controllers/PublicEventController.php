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

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicEventController extends Controller
{
    /**
     * Display the event landing and registration page.
     */
    public function show(string $slug): Response
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->select([
                'id',
                'name',
                'slug',
                'description',
                'venue',
                'starts_at',
                'ends_at',
                'capacity',
                'ticket_types',
                'status',
            ])
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

        if (!$request->filled('ticket_type')) {
            $request->merge(['ticket_type' => $allowedNames[0] ?? 'General Admission']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'ticket_type' => ['required', 'string', \Illuminate\Validation\Rule::in($allowedNames)],
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        // Check if attendee is already registered (Neutralize F3c enumeration oracle & prevent duplicates)
        $existingAttendee = Attendee::where('event_id', $event->id)
            ->where('email', $validated['email'])
            ->first();

        if ($existingAttendee) {
            if ($existingAttendee->ticket) {
                try {
                    GenerateAndSendTicket::dispatch($existingAttendee->ticket, true);
                } catch (\Throwable $e) {
                    Log::warning('Resend to existing attendee failed: ' . $e->getMessage());
                }
            }
            return back()->with('success', 'Registration details received! If already registered, your digital ticket has been resent to your email.');
        }

        $chosenType = $validated['ticket_type'];
        $requestedQuantity = $validated['quantity'];
        $registeredCount = Ticket::whereHas('attendee', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->where('ticket_type', $chosenType)->sum('quantity');

        $categoryConfig = collect($allowedTypes)->firstWhere('name', $chosenType);
        $categoryLimit = $categoryConfig ? ($categoryConfig['capacity'] ?? 100) : 100;

        if (($registeredCount + $requestedQuantity) > $categoryLimit) {
            return back()->withErrors(['quantity' => "Sorry, only " . ($categoryLimit - $registeredCount) . " {$chosenType} tickets are left!"]);
        }

        $seatNumber = strtoupper($chosenType) . '-' . ($registeredCount + 1);
        $unitPrice = $categoryConfig ? ($categoryConfig['price'] ?? 0) : 0;

        try {
            $ticket = DB::transaction(function () use ($event, $validated, $chosenType, $unitPrice, $requestedQuantity, $seatNumber) {
                $attendee = Attendee::create([
                    'event_id' => $event->id,
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);

                do {
                    $token = \Illuminate\Support\Str::random(32);
                } while (Ticket::where('token', $token)->exists());

                return Ticket::create([
                    'attendee_id' => $attendee->id,
                    'token' => $token,
                    'ticket_type' => $chosenType,
                    'price' => $unitPrice * $requestedQuantity,
                    'quantity' => $requestedQuantity,
                    'seat_number' => $seatNumber,
                    'status' => 'issued',
                    'sold_by' => auth()->id(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Registration creation transaction failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Registration could not be completed. Please try again.']);
        }

        // Safely dispatch queued background email job without letting SMTP failures crash HTTP request
        try {
            GenerateAndSendTicket::dispatch($ticket);
        } catch (\Throwable $e) {
            Log::warning('GenerateAndSendTicket dispatch failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Registration successful! Your digital ticket has been generated and sent to your email.');
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

        if ($attendee && $attendee->ticket) {
            try {
                // Dispatch queued job with isResend = true
                GenerateAndSendTicket::dispatch($attendee->ticket, true);
            } catch (\Throwable $e) {
                Log::warning('Resend ticket dispatch failed: ' . $e->getMessage());
            }
        }

        // Uniform response prevents attendee enumeration (closing F3 oracle and preventing F13 error disclosure)
        return back()->with('success', 'If this email address is registered for this event, your ticket has been resent to your inbox.');
    }
}
