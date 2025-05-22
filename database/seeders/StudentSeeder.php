<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specificTeacherId = 2;
        $numberOfStudentsToCreate = 10; // You can adjust this number

        // Find the specific teacher by ID
        $teacher = User::where('id', $specificTeacherId)->whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })->first();

        if (!$teacher) {
            $this->command->error("Teacher with ID {$specificTeacherId} not found or does not have the 'teacher' role. No students were seeded for this ID.");
            // Optionally, you might want to run the old logic or stop
            // For now, we will just stop if the specific teacher is not found.

            // Fallback: Create students for any available teacher if specific one not found
            // $this->command->info("Attempting to seed students for any available teacher as fallback...");
            // $anyTeacher = User::role('teacher')->first();
            // if ($anyTeacher) {
            //     Student::factory()->count($numberOfStudentsToCreate)->create([
            //         'teacher_id' => $anyTeacher->id,
            //     ]);
            //    $this->command->info("Successfully seeded {$numberOfStudentsToCreate} students for teacher ID {$anyTeacher->id} as a fallback.");
            // } else {
            //     $this->command->error('No teachers found to assign students to as a fallback.');
            // }
            return; // Stop if specific teacher not found
        }

        // Create students for the specific teacher
        Student::factory()->count($numberOfStudentsToCreate)->create([
            'teacher_id' => $teacher->id,
        ]);

        $this->command->info("Successfully seeded {$numberOfStudentsToCreate} students for teacher ID {$specificTeacherId} ({$teacher->name}).");

        // The following lines are commented out to focus only on the specific teacher ID
        /*
        // Get all teachers
        $teachers = User::role('teacher')->get();

        if ($teachers->isEmpty()) {
            $this->command->info('No teachers found. Please seed teachers first or ensure teachers exist.');
            return;
        }

        // Create 5 students for each teacher
        $teachers->each(function ($teacher) {
            Student::factory()->count(5)->create([
                'teacher_id' => $teacher->id,
            ]);
        });

        // Create 10 more students and assign them to random teachers
        Student::factory()->count(10)->create();
        */

        // $this->command->info('Student seeding completed.'); // Original message, replaced by more specific one
    }
}
