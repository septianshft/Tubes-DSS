<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentSubmission;
use App\Models\Student;
use App\Models\ScholarshipBatch;

class CreateTestSubmission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-submission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test submission for debugging predefined criteria';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test submission...');

        // Get the open batch
        $batch = ScholarshipBatch::where('status', 'open')->first();
        if (!$batch) {
            $this->error('No open batch found!');
            return;
        }

        $this->info("Using batch: {$batch->name} (ID: {$batch->id})");

        // Get a student
        $student = Student::first();
        if (!$student) {
            $this->error('No student found!');
            return;
        }

        $this->info("Using student: {$student->name} (ID: {$student->id})");

        // Check if submission already exists
        $existingSubmission = StudentSubmission::where('student_id', $student->id)
            ->where('scholarship_batch_id', $batch->id)
            ->first();

        if ($existingSubmission) {
            $this->info("Submission already exists (ID: {$existingSubmission->id})");
            $this->info("Raw criteria values: " . json_encode($existingSubmission->raw_criteria_values));
            $this->info("Normalized scores: " . json_encode($existingSubmission->normalized_scores));
            $this->info("Final SAW score: " . ($existingSubmission->final_saw_score ?? 'Not calculated'));
            return;
        }

        // Create a test submission with some sample data
        $rawCriteriaValues = [
            'criteria_1' => 85,  // Academic achievement
            'criteria_2' => 3,   // Income level option
            'criteria_3' => 2,   // Family size
            'criteria_4' => 75   // Other academic score
        ];

        $submission = StudentSubmission::create([
            'student_id' => $student->id,
            'scholarship_batch_id' => $batch->id,
            'raw_criteria_values' => $rawCriteriaValues,
            'status' => 'submitted'
        ]);

        $this->info("Created new submission (ID: {$submission->id})");
        $this->info("Raw criteria values: " . json_encode($submission->raw_criteria_values));

        $this->info("\nBatch criteria config:");
        $this->info(json_encode($batch->criteria_config, JSON_PRETTY_PRINT));

        $this->info("\nTest submission created successfully!");
        $this->info("You can view it at: http://127.0.0.1:8000/admin/scholarship-batches/{$batch->id}/submissions/{$submission->id}");
    }
}
