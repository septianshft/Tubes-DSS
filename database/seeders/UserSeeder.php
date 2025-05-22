<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user is created in RolesAndPermissionsSeeder
        // Default Teacher user (teacher@example.com) is also created in RolesAndPermissionsSeeder

        // Create a few more Teacher users using the factory
        User::factory()->count(3)->create()->each(function ($user) {
            $user->assignRole('teacher');
        });

        $this->command->info('User seeding completed. Created additional teachers.');
    }
}
