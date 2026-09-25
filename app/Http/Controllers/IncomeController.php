<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role === 'scanner') {
            abort(403, 'Unauthorized action.');
        }

        $query = Event::query();

        if (auth()->user()->role === 'staff') {
            $query->where('created_by', auth()->id());
        }

        $events = $query->withCount('attendees')->latest()->get();

        $incomeData = $events->map(function ($event) {
            $ticketStats = DB::table('tickets')
                ->join('attendees', 'tickets.attendee_id', '=', 'attendees.id')
                ->where('attendees.event_id', $event->id)
                ->select('tickets.ticket_type', DB::raw('COUNT(tickets.id) as count'), DB::raw('SUM(tickets.price) as total_revenue'))
                ->groupBy('tickets.ticket_type')
                ->get();

            $totalIncome = $ticketStats->sum('total_revenue');
            
            return [
                'id' => $event->id,
                'name' => $event->name,
                'status' => $event->status,
                'capacity' => $event->capacity,
                'attendees_count' => $event->attendees_count,
                'total_income' => $totalIncome,
                'breakdown' => $ticketStats,
            ];
        });

        $totalOverallIncome = $incomeData->sum('total_income');

        return Inertia::render('Admin/IncomeIndex', [
            'incomeData' => $incomeData,
            'totalOverallIncome' => $totalOverallIncome,
        ]);
    }
}
