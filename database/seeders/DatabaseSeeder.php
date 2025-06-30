<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Ahmed Aiman',
            'email' => 'aiman@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Seed insulin types
        $this->call([
            InsulinTypeSeeder::class,
        ]);

        // Create test users if in local environment
        if (app()->environment('local')) {
            User::factory(5)->create();
        }
    }
}
