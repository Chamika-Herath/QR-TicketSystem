<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\Ticket;
use App\Models\CheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class AdminEventController extends Controller
{
    /**
     * Display list of events.
     */
    public function index(): Response
    {
        if (auth()->user()->role === 'admin') {
            $events = Event::withCount('attendees')->latest()->get();
        } elseif (auth()->user()->role === 'scanner') {
            $events = Event::where('created_by', auth()->user()->created_by)->withCount('attendees')->latest()->get();
        } else {
            $events = Event::where('created_by', auth()->id())->withCount('attendees')->latest()->get();
        }
        
        return Inertia::render('Admin/EventsIndex', [
            'events' => $events,
        ]);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->role === 'scanner') {
            abort(403, 'Scanners cannot create events.');
        }

        if (auth()->user()->role !== 'admin') {
            $existingCount = Event::where('created_by', auth()->id())->count();
            if ($existingCount >= auth()->user()->allowed_event_limit) {
                return back()->withErrors(['name' => 'You have reached your event creation limit of ' . auth()->user()->allowed_event_limit . ' events.']);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'ticket_template' => 'nullable|image|max:2048',
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.name' => 'required|string|max:255',
            'ticket_types.*.capacity' => 'required|integer|min:1',
            'ticket_types.*.price' => 'required|numeric|min:0',
        ]);

        $ticketTypes = $validated['ticket_types'];
        $overallCapacity = collect($ticketTypes)->sum('capacity');

        if (auth()->user()->role !== 'admin') {
            $totalCapacityUsed = Event::where('created_by', auth()->id())->sum('capacity');
            if (($totalCapacityUsed + $overallCapacity) > auth()->user()->allowed_ticket_limit) {
                return back()->withErrors(['name' => 'This event capacity exceeds your total allowed ticket issuance limit (' . auth()->user()->allowed_ticket_limit . ' tickets).']);
            }
        }

        $slug = Str::slug($validated['name']);
        // Append random string if slug exists
        if (Event::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . Str::lower(Str::random(4));
        }

        $templatePath = null;
        if ($request->hasFile('ticket_template')) {
            $path = $request->file('ticket_template')->store('public/templates');
            $templatePath = Storage::url($path);
        }

        Event::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'venue' => $validated['venue'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'capacity' => $overallCapacity,
            'ticket_template_path' => $templatePath,
            'ticket_types' => $ticketTypes,
            'status' => 'draft',
            'created_by' => auth()->id() ?? 1, // Fallback if seeding or first creation
        ]);

        return redirect()->route('admin.events')->with('success', 'Event created successfully.');
    }

    /**
     * Update the event details.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'ticket_template' => 'nullable|image|max:2048',
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.name' => 'required|string|max:255',
            'ticket_types.*.capacity' => 'required|integer|min:1',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,published,closed',
        ]);

        $ticketTypes = $validated['ticket_types'];
        $overallCapacity = collect($ticketTypes)->sum('capacity');

        if (auth()->user()->role !== 'admin') {
            $totalCapacityUsed = Event::where('created_by', auth()->id())
                ->where('id', '!=', $event->id)
                ->sum('capacity');
            if (($totalCapacityUsed + $overallCapacity) > auth()->user()->allowed_ticket_limit) {
                return back()->withErrors(['name' => 'This event capacity exceeds your total allowed ticket issuance limit (' . auth()->user()->allowed_ticket_limit . ' tickets).']);
            }
        }

        $templatePath = $event->ticket_template_path;
        if ($request->hasFile('ticket_template')) {
            // Delete old if exists
            if ($event->ticket_template_path) {
                Storage::delete(str_replace('/storage', 'public', $event->ticket_template_path));
            }
            $path = $request->file('ticket_template')->store('public/templates');
            $templatePath = Storage::url($path);
        }

        $event->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'venue' => $validated['venue'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'capacity' => $overallCapacity,
            'ticket_template_path' => $templatePath,
            'ticket_types' => $ticketTypes,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Event updated successfully.');
    }

    /**
     * Toggle the status of the event.
     */
    public function toggleStatus(Event $event): RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $event->update([
            'status' => $event->status === 'published' ? 'draft' : 'published',
        ]);

        return back()->with('success', 'Event status updated successfully.');
    }

    /**
     * Delete an event.
     */
    public function destroy(Event $event): RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($event->ticket_template_path) {
            Storage::delete(str_replace('/storage', 'public', $event->ticket_template_path));
        }
        $event->delete();
        return redirect()->route('admin.events')->with('success', 'Event deleted successfully.');
    }

    /**
     * Fetch list of attendees with manual check-in functionality.
     */
    public function attendees(Request $request, Event $event): Response
    {
        $isAuthorized = auth()->user()->role === 'admin' 
            || $event->created_by === auth()->id() 
            || (auth()->user()->role === 'scanner' && $event->created_by === auth()->user()->created_by);

        if (!$isAuthorized) {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->input('search');

        $attendees = Attendee::where('event_id', $event->id)
            ->with(['ticket.checkIn'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/AttendeesIndex', [
            'event' => $event,
            'attendees' => $attendees,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Manual Check-in attendee.
     */
    public function manualCheckin(Request $request, Event $event, Attendee $attendee): RedirectResponse
    {
        $isAuthorized = auth()->user()->role === 'admin' 
            || $event->created_by === auth()->id() 
            || (auth()->user()->role === 'scanner' && $event->created_by === auth()->user()->created_by);

        if (!$isAuthorized) {
            abort(403, 'Unauthorized action.');
        }

        $ticket = $attendee->ticket;
        if (!$ticket) {
            return back()->withErrors(['error' => 'No ticket found for this attendee.']);
        }

        if ($ticket->checkIn) {
            return back()->withErrors(['error' => 'Attendee is already checked in.']);
        }

        CheckIn::create([
            'ticket_id' => $ticket->id,
            'scanned_by' => auth()->id(),
            'scanned_at' => now(),
            'device_info' => 'Manual Check-in',
        ]);

        return back()->with('success', 'Attendee checked in successfully.');
    }

    /**
     * Export attendees as CSV.
     */
    public function exportCsv(Event $event)
    {
        $isAuthorized = auth()->user()->role === 'admin' 
            || $event->created_by === auth()->id() 
            || (auth()->user()->role === 'scanner' && $event->created_by === auth()->user()->created_by);

        if (!$isAuthorized) {
            abort(403, 'Unauthorized action.');
        }

        $attendees = Attendee::where('event_id', $event->id)
            ->with(['ticket.checkIn.scanner'])
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=event-{$event->slug}-attendees.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Name', 'Email', 'Phone', 'Ticket Type', 'Ticket Status', 'Check-In Status', 'Scanned At', 'Scanned By'];

        $callback = function() use($attendees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($attendees as $attendee) {
                $ticket = $attendee->ticket;
                $checkIn = $ticket ? $ticket->checkIn : null;

                fputcsv($file, [
                    $attendee->id,
                    $attendee->name,
                    $attendee->email,
                    $attendee->phone,
                    $ticket ? $ticket->ticket_type : 'N/A',
                    $ticket ? $ticket->status : 'N/A',
                    $checkIn ? 'Checked In' : 'Pending',
                    $checkIn ? $checkIn->scanned_at->format('Y-m-d H:i:s') : '',
                    $checkIn && $checkIn->scanner ? $checkIn->scanner->name : ($checkIn ? 'Manual' : '')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete an attendee from the registry.
     */
    public function destroyAttendee(Event $event, Attendee $attendee): RedirectResponse
    {
        $isAuthorized = auth()->user()->role === 'admin' 
            || $event->created_by === auth()->id() 
            || (auth()->user()->role === 'scanner' && $event->created_by === auth()->user()->created_by);

        if (!$isAuthorized) {
            abort(403, 'Unauthorized action.');
        }

        if ($attendee->event_id !== $event->id) {
            abort(400, 'Invalid request.');
        }

        $attendee->delete();

        return back()->with('success', 'Attendee removed from registry successfully.');
    }
}
