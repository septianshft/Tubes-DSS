<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentSubmission;
use App\Models\Student;
use App\Models\ScholarshipBatch;
use App\Models\User;

echo "=== CREATING TEST SUBMISSION ===\n";

// Get the open batch
$batch = ScholarshipBatch::where('status', 'open')->first();
if (!$batch) {
    echo "No open batch found!\n";
    exit;
}

echo "Using batch: {$batch->name} (ID: {$batch->id})\n";

// Get a student
$student = Student::first();
if (!$student) {
    echo "No student found!\n";
    exit;
}

echo "Using student: {$student->name} (ID: {$student->id})\n";

// Check if submission already exists
$existingSubmission = StudentSubmission::where('student_id', $student->id)
    ->where('scholarship_batch_id', $batch->id)
    ->first();

if ($existingSubmission) {
    echo "Submission already exists (ID: {$existingSubmission->id})\n";
    echo "Raw criteria values: " . json_encode($existingSubmission->raw_criteria_values) . "\n";
    echo "Normalized scores: " . json_encode($existingSubmission->normalized_scores) . "\n";
    echo "Final SAW score: " . ($existingSubmission->final_saw_score ?? 'Not calculated') . "\n";
} else {
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

    echo "Created new submission (ID: {$submission->id})\n";
    echo "Raw criteria values: " . json_encode($submission->raw_criteria_values) . "\n";
}

echo "\n=== BATCH CRITERIA CONFIG ===\n";
echo json_encode($batch->criteria_config, JSON_PRETTY_PRINT) . "\n";
