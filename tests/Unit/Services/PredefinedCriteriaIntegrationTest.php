<?php

namespace Tests\Unit\Services;

use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\StudentSubmission;
use App\Models\User;
use App\Services\SAWCalculatorService;
use App\Services\PredefinedCriteriaService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class PredefinedCriteriaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected SAWCalculatorService $sawCalculatorService;
    protected PredefinedCriteriaService $predefinedCriteriaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->predefinedCriteriaService = new PredefinedCriteriaService();
        $this->sawCalculatorService = new SAWCalculatorService($this->predefinedCriteriaService);

        // Create required roles
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    /**
     * Test predefined criteria average_score calculation
     */
    public function testPredefinedAverageScoreCriterion()
    {
        // Create a scholarship batch with predefined average_score criterion
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'average_score', // This is a predefined criterion
                    'name' => 'Nilai Rata-Rata Siswa',
                    'weight' => 1.0,
                    'type' => 'benefit',
                    'data_type' => 'numeric'
                ]
            ]
        ]);

        // Create students with different average scores
        $student1 = Student::factory()->create(['average_score' => 85.0]); // Should get score 5 (80.01-100 range)
        $student2 = Student::factory()->create(['average_score' => 75.0]); // Should get score 4 (70.01-80 range)
        $student3 = Student::factory()->create(['average_score' => 65.0]); // Should get score 3 (60.01-70 range)

        // Create teacher for submissions
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create submissions (for predefined criteria, raw_criteria_values can be empty since data comes from Student model)
        StudentSubmission::factory()->create([
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => [] // Empty for predefined criteria
        ]);

        StudentSubmission::factory()->create([
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => []
        ]);

        StudentSubmission::factory()->create([
            'student_id' => $student3->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => []
        ]);

        // Calculate scores
        $score1 = $this->sawCalculatorService->calculateScore($student1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($student2, $batch);
        $score3 = $this->sawCalculatorService->calculateScore($student3, $batch);

        // Expected scores based on predefined criteria scoring (converted to 0-1 scale):
        // Student1 (85.0): score 5 -> 5/5 = 1.0
        // Student2 (75.0): score 4 -> 4/5 = 0.8
        // Student3 (65.0): score 3 -> 3/5 = 0.6
        $this->assertEquals(1.0, $score1, 'Student with 85.0 average should get max score (1.0)');
        $this->assertEquals(0.8, $score2, 'Student with 75.0 average should get score 0.8');
        $this->assertEquals(0.6, $score3, 'Student with 65.0 average should get score 0.6');
    }

    /**
     * Test predefined criteria tuition_payment_delays calculation
     */
    public function testPredefinedTuitionPaymentDelaysCriterion()
    {
        // Create a scholarship batch with predefined tuition_payment_delays criterion
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'tuition_payment_delays', // This is a predefined criterion
                    'name' => 'Keterlambatan Pembayaran SPP',
                    'weight' => 1.0,
                    'type' => 'cost', // Lower delays are better
                    'data_type' => 'qualitative_option'
                ]
            ]
        ]);

        // Create students with different payment delay counts
        $student1 = Student::factory()->create(['tuition_payment_delays' => 0]); // Should map to "on_time" -> score 5
        $student2 = Student::factory()->create(['tuition_payment_delays' => 1]); // Should map to "late_under_1_month" -> score 4
        $student3 = Student::factory()->create(['tuition_payment_delays' => 3]); // Should map to "late_2_3_months" -> score 2

        // Create teacher for submissions
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create submissions
        StudentSubmission::factory()->create([
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => []
        ]);

        StudentSubmission::factory()->create([
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => []
        ]);

        StudentSubmission::factory()->create([
            'student_id' => $student3->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => []
        ]);

        // Calculate scores
        $score1 = $this->sawCalculatorService->calculateScore($student1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($student2, $batch);
        $score3 = $this->sawCalculatorService->calculateScore($student3, $batch);

        // Expected scores based on predefined criteria scoring (converted to 0-1 scale):
        // Student1 (0 delays): "on_time" -> score 5 -> 5/5 = 1.0
        // Student2 (1 delay): "late_under_1_month" -> score 4 -> 4/5 = 0.8
        // Student3 (3 delays): "late_2_3_months" -> score 2 -> 2/5 = 0.4
        $this->assertEquals(1.0, $score1, 'Student with 0 delays should get max score (1.0)');
        $this->assertEquals(0.8, $score2, 'Student with 1 delay should get score 0.8');
        $this->assertEquals(0.4, $score3, 'Student with 3 delays should get score 0.4');
    }

    /**
     * Test mixed predefined and custom criteria
     */
    public function testMixedPredefinedAndCustomCriteria()
    {
        // Create a scholarship batch with both predefined and custom criteria
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'average_score', // Predefined
                    'name' => 'Nilai Rata-Rata Siswa',
                    'weight' => 0.6,
                    'type' => 'benefit',
                    'data_type' => 'numeric'
                ],                [
                    'id' => 'custom_financial_need', // Custom criterion
                    'name' => 'Financial Need Score',
                    'weight' => 0.4,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                    'min_value' => 0,
                    'max_value' => 100
                ]
            ]
        ]);        // Create students
        $student = Student::factory()->create([
            'average_score' => 85.0, // Predefined: score 5 -> 1.0
        ]);

        $studentMin = Student::factory()->create([
            'average_score' => 75.0, // For min/max calculation
        ]);

        $studentMax = Student::factory()->create([
            'average_score' => 85.0, // For min/max calculation
        ]);        // Create teacher for submissions
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create submissions (custom criteria need values in raw_criteria_values)
        $financialScores = [80.0, 60.0, 90.0]; // For student, studentMin, studentMax respectively
        foreach ([$student, $studentMin, $studentMax] as $index => $s) {
            StudentSubmission::factory()->create([
                'student_id' => $s->id,
                'scholarship_batch_id' => $batch->id,
                'submitted_by_teacher_id' => $teacher->id,
                'raw_criteria_values' => [
                    'custom_financial_need' => $financialScores[$index]
                ]
            ]);
        }

        // Calculate score for main student
        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        // Expected calculation:
        // Predefined average_score (85.0): score 5 -> 5/5 = 1.0, weighted: 1.0 * 0.6 = 0.6
        // Custom financial_need (80.0): (80-60)/(90-60) = 20/30 = 0.667, weighted: 0.667 * 0.4 = 0.267
        // Total: 0.6 + 0.267 = 0.867
        $expectedScore = (1.0 * 0.6) + ((20.0/30.0) * 0.4);
        $this->assertEquals(round($expectedScore, 4), $score, 'Mixed criteria calculation should be correct');
    }

    /**
     * Test extracurricular_activeness scale conversion
     */
    public function testExtracurricularActivenessScaleConversion()
    {
        // Create a scholarship batch with predefined extracurricular_activeness criterion
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'extracurricular_activeness', // This is a predefined criterion
                    'name' => 'Keaktifan Ekstrakurikuler',
                    'weight' => 1.0,
                    'type' => 'benefit',
                    'data_type' => 'numeric'
                ]
            ]
        ]);

        // Create students with different extracurricular activeness (1-5 scale)
        $student1 = Student::factory()->create(['extracurricular_activeness' => 5]); // Should convert to 100% -> score 5
        $student2 = Student::factory()->create(['extracurricular_activeness' => 3]); // Should convert to 60% -> score 3
        $student3 = Student::factory()->create(['extracurricular_activeness' => 1]); // Should convert to 20% -> score 1

        // Create teacher for submissions
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create submissions
        foreach ([$student1, $student2, $student3] as $student) {
            StudentSubmission::factory()->create([
                'student_id' => $student->id,
                'scholarship_batch_id' => $batch->id,
                'submitted_by_teacher_id' => $teacher->id,
                'raw_criteria_values' => []
            ]);
        }

        // Calculate scores
        $score1 = $this->sawCalculatorService->calculateScore($student1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($student2, $batch);
        $score3 = $this->sawCalculatorService->calculateScore($student3, $batch);

        // Expected scores based on scale conversion and predefined scoring:
        // Student1 (5): 100% -> score 5 -> 5/5 = 1.0
        // Student2 (3): 60% -> score 3 -> 3/5 = 0.6
        // Student3 (1): 20% -> score 1 -> 1/5 = 0.2
        $this->assertEquals(1.0, $score1, 'Student with extracurricular_activeness 5 should get max score');
        $this->assertEquals(0.6, $score2, 'Student with extracurricular_activeness 3 should get score 0.6');
        $this->assertEquals(0.2, $score3, 'Student with extracurricular_activeness 1 should get score 0.2');
    }
}
