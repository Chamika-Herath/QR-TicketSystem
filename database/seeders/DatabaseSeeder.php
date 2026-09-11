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

        // Seed admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Seed staff user
        User::create([
            'name' => 'Staff Scanner',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        // Seed initial published demo event
        \App\Models\Event::create([
            'name' => 'Tech Synergy Conference 2026',
            'slug' => 'tech-synergy-2026',
            'description' => 'The premier gathering for developers, creators, and technologists to explore the next generation of fullstack apps.',
            'venue' => 'Grand Millennium Hall, San Francisco',
            'starts_at' => now()->addDays(5),
            'ends_at' => now()->addDays(5)->addHours(8),
            'capacity' => 150,
            'status' => 'published',
            'created_by' => $admin->id,
        ]);
    }
}
