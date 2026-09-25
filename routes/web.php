<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\IncomeController;

// Public event routes
Route::get('/events/{slug}', [PublicEventController::class, 'show'])->name('events.show');
Route::post('/events/{slug}/register', [PublicEventController::class, 'register'])->name('events.register');
Route::get('/tickets/resend', [PublicEventController::class, 'showResend'])->name('tickets.resend.show');
Route::post('/tickets/resend', [PublicEventController::class, 'resend'])->name('tickets.resend');

// Staff & Admin Sanctum-protected Dashboard & Scanning routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Override default dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/events/{event}/stats', [DashboardController::class, 'stats'])->name('api.events.stats');

    // Scanning & Checkin
    Route::get('/scanner', [CheckInController::class, 'showScanner'])->name('scanner');
    Route::post('/api/checkin/{token}', [CheckInController::class, 'checkin'])->name('api.checkin');

    // Admin & Staff operations
    Route::get('/events/{event}/attendees', [AdminEventController::class, 'attendees'])->name('admin.events.attendees');
    Route::delete('/events/{event}/attendees/{attendee}', [AdminEventController::class, 'destroyAttendee'])->name('admin.events.attendees.destroy');
    Route::post('/events/{event}/attendees/{attendee}/manual-checkin', [AdminEventController::class, 'manualCheckin'])->name('admin.events.manual-checkin');
    Route::get('/events/{event}/export', [AdminEventController::class, 'exportCsv'])->name('admin.events.export');

    // Admin only CRUD (add middleware check if required, or simply restrict via role checking in controller/Inertia)
    Route::get('/events', [AdminEventController::class, 'index'])->name('admin.events');
    Route::post('/events', [AdminEventController::class, 'store'])->name('admin.events.store');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('admin.events.update');
    Route::patch('/events/{event}/status', [AdminEventController::class, 'toggleStatus'])->name('admin.events.toggle-status');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');

    // Admin only User Management CRUD
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('admin.users');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('admin.users.destroy');

    // Income & Reports
    Route::get('/income', [IncomeController::class, 'index'])->name('admin.income');
});

// Front page route
Route::get('/', function () {
    try {
        $events = \App\Models\Event::where('status', 'published')
            ->orderBy('starts_at', 'asc')
            ->get();
    } catch (\Throwable $e) {
        $events = collect();
    }
    return Inertia\Inertia::render('Welcome', [
        'events' => $events,
    ]);
})->name('home');

Route::get('/api/events', function () {
    try {
        $events = \App\Models\Event::where('status', 'published')
            ->orderBy('starts_at', 'asc')
            ->get();
    } catch (\Throwable $e) {
        $events = collect();
    }
    return response()->json($events);
})->name('api.events');

require __DIR__.'/settings.php';
Route::get('/auth', function () { return Route::has('login'); }); // Simple routing check
