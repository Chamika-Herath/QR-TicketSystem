<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\Ticket;
use App\Models\CheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Display the Admin/Staff Dashboard.
     */
    public function index(Request $request): Response
    {
        if (auth()->user()->role === 'admin') {
            $events = Event::orderBy('name')->get(['id', 'name', 'slug']);
        } else {
            $events = Event::where('created_by', auth()->id())->orderBy('name')->get(['id', 'name', 'slug']);
        }
        
        $selectedEventId = $request->input('event_id');
        if (!$selectedEventId && $events->isNotEmpty()) {
            $selectedEventId = $events->first()->id;
        }

        $event = $selectedEventId ? Event::find($selectedEventId) : null;

        // Extra check if staff tries to fetch details of someone else's event
        if ($event && auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {
            $event = null;
            $selectedEventId = null;
        }

        return Inertia::render('Admin/Dashboard', [
            'events' => $events,
            'selectedEventId' => (int) $selectedEventId,
            'event' => $event,
        ]);
    }

    /**
     * Get Stats API for the selected event.
     */
    public function stats(Event $event): JsonResponse
    {
        if (auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $totalRegistered = Attendee::where('event_id', $event->id)->count();
        
        $totalCheckedIn = CheckIn::whereHas('ticket.attendee', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->count();

        // Hourly check-ins breakdown (for the line chart)
        // Group by hour of check-in
        $checkInsPerHour = CheckIn::whereHas('ticket.attendee', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })
        ->select(DB::raw("DATE_FORMAT(scanned_at, '%Y-%m-%d %H:00:00') as hour"), DB::raw('count(*) as count'))
        ->groupBy('hour')
        ->orderBy('hour', 'asc')
        ->get();

        $ticketsByType = Ticket::whereHas('attendee', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })
        ->select('ticket_type', DB::raw('count(*) as count'), DB::raw('sum(price) as income'))
        ->groupBy('ticket_type')
        ->get();

        $totalIncome = Ticket::whereHas('attendee', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->sum('price');

        return response()->json([
            'total_registered' => $totalRegistered,
            'total_checked_in' => $totalCheckedIn,
            'capacity' => $event->capacity,
            'hourly_data' => $checkInsPerHour,
            'tickets_by_type' => $ticketsByType,
            'total_income' => $totalIncome,
        ]);
    }
}
