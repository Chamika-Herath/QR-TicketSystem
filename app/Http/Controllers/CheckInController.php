<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\CheckIn;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class CheckInController extends Controller
{
    public function showScanner(): Response
    {
        $todayCount = CheckIn::whereDate('scanned_at', now()->toDateString())->count();
        return Inertia::render('Staff/Scanner', [
            'todayCount' => $todayCount,
        ]);
    }

    /**
     * Handle the scanning of a ticket QR token.
     */
    public function checkin(Request $request, string $token): JsonResponse
    {
        $ticket = Ticket::where('token', $token)->with('attendee.event')->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Invalid or unrecognized QR token.'
            ], 404);
        }

        $isAuthorized = false;
        if (auth()->user()->role === 'admin') {
            $isAuthorized = true;
        } elseif (auth()->user()->role === 'staff' && $ticket->attendee->event->created_by === auth()->id()) {
            $isAuthorized = true;
        } elseif (auth()->user()->role === 'scanner' && $ticket->attendee->event->created_by === auth()->user()->created_by) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'This ticket is not for an event you manage.'
            ], 403);
        }

        if ($ticket->status === 'revoked') {
            return response()->json([
                'status' => 'invalid',
                'message' => 'This ticket has been revoked.'
            ], 400);
        }

        // Check existing check-ins
        $totalCheckedIn = CheckIn::where('ticket_id', $ticket->id)->sum('entries');
        $availableQuantity = $ticket->quantity - $totalCheckedIn;

        if ($availableQuantity <= 0) {
            $lastCheckIn = CheckIn::where('ticket_id', $ticket->id)->latest()->first();
            return response()->json([
                'status' => 'already_checked_in',
                'message' => 'All tickets under this token have already been checked in.',
                'scanned_at' => $lastCheckIn ? $lastCheckIn->scanned_at->format('M d, Y h:i A') : '',
                'attendee' => [
                    'name' => $ticket->attendee->name,
                    'email' => $ticket->attendee->email,
                    'event_name' => $ticket->attendee->event->name,
                ]
            ], 409);
        }

        $entries = $request->input('entries', null);

        if ($availableQuantity > 1 && $entries === null) {
            return response()->json([
                'status' => 'requires_quantity',
                'message' => 'Multiple tickets available. Select quantity to check in.',
                'available_quantity' => $availableQuantity,
                'attendee' => [
                    'name' => $ticket->attendee->name,
                    'email' => $ticket->attendee->email,
                    'event_name' => $ticket->attendee->event->name,
                ]
            ], 200);
        }

        $entries = (int) ($entries ?? 1);

        if ($entries > $availableQuantity) {
            return response()->json([
                'status' => 'invalid',
                'message' => "Cannot check in $entries people. Only $availableQuantity left."
            ], 400);
        }

        // Perform Check-in
        $checkIn = CheckIn::create([
            'ticket_id' => $ticket->id,
            'scanned_by' => auth()->id(),
            'scanned_at' => now(),
            'device_info' => $request->header('User-Agent'),
            'entries' => $entries,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in successful! Welcome to the event.',
            'attendee' => [
                'name' => $ticket->attendee->name,
                'email' => $ticket->attendee->email,
                'event_name' => $ticket->attendee->event->name,
                'total_quantity' => $ticket->quantity,
                'checked_in_now' => $entries,
                'remaining_quantity' => $availableQuantity - $entries,
            ]
        ], 200);
    }
}
