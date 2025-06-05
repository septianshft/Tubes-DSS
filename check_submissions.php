<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use App\Models\StudentSubmission;

echo "Submissions: " . StudentSubmission::count() . "\n";

StudentSubmission::with(['student', 'scholarshipBatch'])->get()->each(function($s) {
    echo "ID: {$s->id}, Student: {$s->student->name}, Batch: {$s->scholarshipBatch->name}\n";
});

echo "\nFirst submission details:\n";
$first = StudentSubmission::with(['student', 'scholarshipBatch'])->first();
if ($first) {
    echo "Raw criteria values: " . json_encode($first->raw_criteria_values) . "\n";
    echo "Normalized scores: " . json_encode($first->normalized_scores) . "\n";
    echo "Final SAW score: {$first->final_saw_score}\n";
}
