<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentSubmission;
use App\Models\Student;
use App\Models\ScholarshipBatch;

echo "🔍 Searching for Submission ID 7...\n";
echo "====================================\n\n";

try {
    // Find submission with ID 7
    $submission = StudentSubmission::with(['student', 'scholarshipBatch'])->find(7);

    if (!$submission) {
        echo "❌ Submission with ID 7 not found!\n";

        // Show all available submissions
        echo "\n📋 Available submissions:\n";
        $allSubmissions = StudentSubmission::with('student')->get();
        foreach ($allSubmissions as $sub) {
            echo "ID: {$sub->id} | Student: {$sub->student->name ?? 'N/A'} | Batch: {$sub->scholarship_batch_id}\n";
        }
        exit;
    }

    echo "✅ Found Submission ID 7!\n";
    echo "========================\n\n";

    // Basic submission info
    echo "📊 SUBMISSION DETAILS:\n";
    echo "-----------------------\n";
    echo "ID: {$submission->id}\n";
    echo "Student ID: {$submission->student_id}\n";
    echo "Student Name: " . ($submission->student->name ?? 'N/A') . "\n";
    echo "Student Email: " . ($submission->student->email ?? 'N/A') . "\n";
    echo "Scholarship Batch ID: {$submission->scholarship_batch_id}\n";
    echo "Batch Name: " . ($submission->scholarshipBatch->name ?? 'N/A') . "\n";
    echo "Status: {$submission->status}\n";
    echo "Final SAW Score: " . ($submission->final_saw_score ?? 'Not calculated') . "\n";
    echo "Submitted At: {$submission->submitted_at}\n";
    echo "Created At: {$submission->created_at}\n";
    echo "Updated At: {$submission->updated_at}\n";

    // Raw criteria values
    echo "\n📝 RAW CRITERIA VALUES:\n";
    echo "------------------------\n";
    if ($submission->raw_criteria_values && is_array($submission->raw_criteria_values)) {
        foreach ($submission->raw_criteria_values as $criterion => $value) {
            echo "• {$criterion}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
    } else {
        echo "No raw criteria values found.\n";
    }

    // Normalized scores
    echo "\n🔢 NORMALIZED SCORES:\n";
    echo "---------------------\n";
    if ($submission->normalized_scores) {
        if (is_string($submission->normalized_scores)) {
            $normalizedScores = json_decode($submission->normalized_scores, true);
        } else {
            $normalizedScores = $submission->normalized_scores;
        }

        if ($normalizedScores && is_array($normalizedScores)) {
            foreach ($normalizedScores as $criterion => $score) {
                echo "• {$criterion}: {$score}\n";
            }
        } else {
            echo "No normalized scores or invalid format.\n";
        }
    } else {
        echo "No normalized scores calculated yet.\n";
    }

    // Student details
    echo "\n👤 STUDENT DETAILS:\n";
    echo "-------------------\n";
    if ($submission->student) {
        $student = $submission->student;
        echo "Name: {$student->name}\n";
        echo "Email: {$student->email}\n";
        echo "Student ID: {$student->student_id}\n";
        echo "Class Attendance: " . ($student->class_attendance_percentage ?? 'N/A') . "%\n";
        echo "Average Score: " . ($student->average_score ?? 'N/A') . "\n";
        echo "Extracurricular Score: " . ($student->extracurricular_score ?? 'N/A') . "\n";
    }

    // Batch details
    echo "\n🎓 SCHOLARSHIP BATCH DETAILS:\n";
    echo "-----------------------------\n";
    if ($submission->scholarshipBatch) {
        $batch = $submission->scholarshipBatch;
        echo "Name: {$batch->name}\n";
        echo "Description: " . ($batch->description ?? 'N/A') . "\n";
        echo "Status: {$batch->status}\n";
        echo "Submission Deadline: {$batch->submission_deadline}\n";

        echo "\n🔧 CRITERIA CONFIGURATION:\n";
        if ($batch->criteria_config && is_array($batch->criteria_config)) {
            foreach ($batch->criteria_config as $i => $criterion) {
                echo "  Criterion " . ($i + 1) . ":\n";
                echo "    ID: " . ($criterion['id'] ?? 'N/A') . "\n";
                echo "    Name: " . ($criterion['name'] ?? 'N/A') . "\n";
                echo "    Type: " . ($criterion['type'] ?? 'N/A') . "\n";
                echo "    Weight: " . ($criterion['weight'] ?? 'N/A') . "\n";
                echo "    Data Type: " . ($criterion['data_type'] ?? 'N/A') . "\n";
                if (isset($criterion['options']) && is_array($criterion['options'])) {
                    echo "    Options:\n";
                    foreach ($criterion['options'] as $option) {
                        echo "      - " . ($option['value'] ?? 'N/A') . " (numeric: " . ($option['numeric_value'] ?? 'N/A') . ")\n";
                    }
                }
                echo "\n";
            }
        } else {
            echo "No criteria configuration found.\n";
        }
    }

    // Other submissions in the same batch for comparison
    echo "\n📊 OTHER SUBMISSIONS IN SAME BATCH:\n";
    echo "------------------------------------\n";
    $otherSubmissions = StudentSubmission::where('scholarship_batch_id', $submission->scholarship_batch_id)
                                        ->with('student')
                                        ->get();

    foreach ($otherSubmissions as $sub) {
        $status = $sub->id == 7 ? " ← THIS ONE" : "";
        echo "ID: {$sub->id} | Student: " . ($sub->student->name ?? 'N/A') . " | Score: " . ($sub->final_saw_score ?? 'N/A') . "{$status}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✅ Search complete!\n";
