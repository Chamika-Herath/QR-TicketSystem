<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. Seed Admin user (System Owner)
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 2. Seed Staff user (Event Organizer)
        $staff = User::create([
            'name' => 'Event Organizer',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'allowed_event_limit' => 5,
            'allowed_ticket_limit' => 500,
            'created_by' => $admin->id,
        ]);

        // 3. Seed Scanner user (Created by Staff)
        $scanner = User::create([
            'name' => 'Gate Scanner 1',
            'email' => 'scanner@example.com',
            'password' => bcrypt('password'),
            'role' => 'scanner',
            'allowed_event_limit' => 0,
            'allowed_ticket_limit' => 0,
            'created_by' => $staff->id,
        ]);

        // 4. Seed initial published demo event (Owned by Staff)
        $event = \App\Models\Event::create([
            'name' => 'Tech Synergy Conference 2026',
            'slug' => 'tech-synergy-2026',
            'description' => 'The premier gathering for developers, creators, and technologists to explore the next generation of fullstack apps.',
            'venue' => 'Grand Millennium Hall, San Francisco',
            'starts_at' => now()->addDays(5),
            'ends_at' => now()->addDays(5)->addHours(8),
            'capacity' => 150,
            'status' => 'published',
            'created_by' => $staff->id,
            'ticket_types' => [
                ['name' => 'Early Bird', 'capacity' => 50, 'price' => 1500],
                ['name' => 'General Admission', 'capacity' => 80, 'price' => 3000],
                ['name' => 'VIP', 'capacity' => 20, 'price' => 7500],
            ],
        ]);
    }
}
