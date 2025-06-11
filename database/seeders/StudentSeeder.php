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
        $teacherUser = User::where('email', 'teacher@example.com')->first();

        if (!$teacherUser) {
            $this->command->error("Teacher with email 'teacher@example.com' not found. Cannot seed students.");
            return;
        }

        $studentNames = [
            "Arya Pratama",
            "Nabila Zahra",
            "Rizky Aditya",
            "Siti Mawar",
            "Dimas Prakoso",
            "Intan Permata",
            "Galih Nugraha",
            "Shafira Annisa",
            "Bima Aryono",
            "Amanda Lestari", // First Amanda
            "Amanda Lestari", // Second Amanda
            "Farhan Maulana",
            "Putri Melati",
            "Yusuf Ramadhan",
            "Laras Ayu",
            "Kevin Wijaya",
            "Aulia Rahma",
            "Raka Saputra",
            "Maya Salsabila",
            "Andre Surya"
        ];

        $createdCount = 0;
        foreach ($studentNames as $name) {
            Student::factory()->create([
                'name' => $name,
                'teacher_id' => $teacherUser->id,
                // Other attributes will be faked by the factory
            ]);
            $createdCount++;
        }

        $this->command->info("Successfully seeded {$createdCount} students with predefined names for teacher: {$teacherUser->name} ({$teacherUser->email}).");
    }
}
