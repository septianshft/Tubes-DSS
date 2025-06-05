<?php

use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\StudentSubmission;
use App\Models\User;
use App\Services\SAWCalculatorService;
use App\Services\PredefinedCriteriaService;
use Spatie\Permission\Models\Role;

try {
    require_once 'vendor/autoload.php';

    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "Laravel bootstrapped successfully\n";

// Create roles
Role::firstOrCreate(['name' => 'teacher']);

// Create a batch with average_score criterion
$batch = ScholarshipBatch::factory()->create([
    'criteria_config' => [
        [
            'id' => 'average_score',
            'student_attribute' => 'average_score',
            'name' => 'Average Score',
            'weight' => 1.0,
            'type' => 'benefit',
            'data_type' => 'numeric',
            'min_value' => 0,
            'max_value' => 100
        ]
    ]
]);

// Create students
$student1 = Student::factory()->create(['average_score' => 85.0]);
$student2 = Student::factory()->create(['average_score' => 95.0]);

// Create teacher
$teacher = User::factory()->create();
$teacher->assignRole('teacher');

// Create submissions
$submission1 = StudentSubmission::factory()->create([
    'student_id' => $student1->id,
    'scholarship_batch_id' => $batch->id,
    'submitted_by_teacher_id' => $teacher->id,
    'raw_criteria_values' => ['average_score' => 85.0]
]);

$submission2 = StudentSubmission::factory()->create([
    'student_id' => $student2->id,
    'scholarship_batch_id' => $batch->id,
    'submitted_by_teacher_id' => $teacher->id,
    'raw_criteria_values' => ['average_score' => 95.0]
]);

// Initialize service
$predefinedCriteriaService = new PredefinedCriteriaService();
$sawService = new SAWCalculatorService($predefinedCriteriaService);

// Debug: Check what values are being retrieved
echo "=== DEBUG: Raw Values ===\n";
echo "Student 1 average_score from model: " . $student1->average_score . "\n";
echo "Student 2 average_score from model: " . $student2->average_score . "\n";

// Test the getRawValueForPredefinedCriterion method directly
// Note: The method is private, but we can test via calculateScore which uses it internally
// We'll instead test the actual scoring flow
echo "Testing predefined criteria scoring through calculateScore...\n";

echo "Raw value from model - Student 1: " . $student1->average_score . "\n";
echo "Raw value from model - Student 2: " . $student2->average_score . "\n";

// Calculate scores
echo "\n=== DEBUG: Scores ===\n";
$score1 = $sawService->calculateScore($student1, $batch);
$score2 = $sawService->calculateScore($student2, $batch);

echo "Score 1: $score1\n";
echo "Score 2: $score2\n";

echo "\n=== Expected vs Actual ===\n";
echo "Expected: Student1=0.0, Student2=1.0\n";
echo "Actual: Student1=$score1, Student2=$score2\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
