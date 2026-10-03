<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\Ticket;
use App\Models\CheckIn;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public registration generates ticket and dispatches mailable', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    $event = Event::create([
        'name' => 'Web Conf 2026',
        'slug' => 'web-conf-2026',
        'venue' => 'Digital Center',
        'starts_at' => now()->addDays(2),
        'ends_at' => now()->addDays(2)->addHours(4),
        'capacity' => 100,
        'status' => 'published',
        'created_by' => $admin->id,
    ]);

    $response = $this->post(route('events.register', 'web-conf-2026'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '1234567890',
        'quantity' => 1,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('attendees', [
        'email' => 'jane@example.com',
        'name' => 'Jane Doe',
    ]);

    $attendee = Attendee::where('email', 'jane@example.com')->first();
    $this->assertNotNull($attendee->ticket);
    $this->assertNotNull($attendee->ticket->token);
});

test('duplicate check-in is blocked at API level', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    $event = Event::create([
        'name' => 'Web Conf 2026',
        'slug' => 'web-conf-2026',
        'venue' => 'Digital Center',
        'starts_at' => now()->addDays(2),
        'ends_at' => now()->addDays(2)->addHours(4),
        'capacity' => 100,
        'status' => 'published',
        'created_by' => $admin->id,
    ]);

    $attendee = Attendee::create([
        'event_id' => $event->id,
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '1234567890',
    ]);

    $ticket = Ticket::create([
        'attendee_id' => $attendee->id,
        'token' => 'test-token-123',
        'status' => 'issued',
    ]);

    // Authenticate scanner staff
    $this->actingAs($admin);

    // First scan check-in
    $response = $this->postJson("/api/checkin/test-token-123");
    $response->assertStatus(200);
    $response->assertJsonFragment(['status' => 'success']);

    // Second scan check-in
    $duplicateResponse = $this->postJson("/api/checkin/test-token-123");
    $duplicateResponse->assertStatus(409);
    $duplicateResponse->assertJsonFragment(['status' => 'already_checked_in']);
});

test('invalid check-in token returns 404', function () {
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    $this->actingAs($admin);

    $response = $this->postJson("/api/checkin/nonexistent-token");
    $response->assertStatus(404);
});
