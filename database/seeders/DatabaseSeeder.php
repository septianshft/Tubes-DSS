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
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class, // Add UserSeeder
            StudentSeeder::class, // Ensure StudentSeeder is called
            // ScholarshipBatchSeeder::class, // You may want to uncomment this later
            // StudentSubmissionSeeder::class, // You may want to uncomment this later
        ]);
    }
}
