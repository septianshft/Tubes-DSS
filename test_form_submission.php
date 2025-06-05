<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\User;
use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\StudentSubmission;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing form submission scenario...\n";

// Get teacher
$teacher = User::where('email', 'teacher@example.com')->first();
echo "Teacher: {$teacher->name} (ID: {$teacher->id})\n";

// Get batch
$batch = ScholarshipBatch::where('name', 'Opsis 1')->first();
echo "Batch: {$batch->name} (ID: {$batch->id})\n";
echo "Batch Status: {$batch->status}\n";
echo "Batch Criteria: " . count($batch->criteria_config ?? []) . " criteria\n";

// Get students for this teacher
$students = Student::where('teacher_id', $teacher->id)->take(2)->get();
echo "Students (" . count($students) . "):\n";
foreach ($students as $student) {
    echo "  - {$student->name} (ID: {$student->id})\n";
}

// Check existing submissions
$existingSubmissions = StudentSubmission::where('scholarship_batch_id', $batch->id)
    ->whereIn('student_id', $students->pluck('id'))
    ->count();
echo "Existing submissions for these students: {$existingSubmissions}\n";

echo "\nForm should work with this data.\n";
