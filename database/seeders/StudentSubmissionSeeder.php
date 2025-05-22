<?php

namespace Database\Seeders;

use App\Models\StudentSubmission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a variety of submissions
        // For example, 50 submissions. Adjust as needed.
        StudentSubmission::factory()->count(50)->create();
    }
}
