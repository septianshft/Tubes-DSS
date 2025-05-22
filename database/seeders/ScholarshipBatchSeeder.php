<?php

namespace Database\Seeders;

use App\Models\ScholarshipBatch; // Add this line
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScholarshipBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScholarshipBatch::factory()->count(2)->create();
        $this->command->info('Scholarship batch seeding completed. Created 2 batches.');
    }
}
