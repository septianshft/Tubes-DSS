<?php

namespace Tests\Unit\Services;

use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\User;
use App\Services\SAWCalculatorService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class SAWCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SAWCalculatorService $sawCalculatorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sawCalculatorService = new SAWCalculatorService();

        // Create required roles
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    /**
     * Test empty criteria configuration returns 0
     */
    public function testEmptyCriteriaConfig()
    {
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => []
        ]);

        $student = Student::factory()->create();

        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        $this->assertEquals(0.0, $score);
    }

    /**
     * Test null criteria configuration returns 0
     */
    public function testNullCriteriaConfig()
    {
        // Since criteria_config is not nullable in migration, we'll test with empty array instead
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => []
        ]);

        $student = Student::factory()->create();

        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        $this->assertEquals(0.0, $score);
    }

    /**
     * Test calculation with single benefit criterion and real data
     */
    public function testSingleBenefitCriterionWithRealData()
    {
        // Create a scholarship batch with realistic criteria config
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

        // Create a teacher
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create students with known scores
        $student1 = Student::factory()->create(['average_score' => 80.0]);
        $student2 = Student::factory()->create(['average_score' => 90.0]);

        // Create submissions for normalization
        $submission1 = \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['average_score' => 80.0]
        ]);

        $submission2 = \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['average_score' => 90.0]
        ]);

        // Calculate scores
        $score1 = $this->sawCalculatorService->calculateScore($student1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($student2, $batch);

        // Expected:
        // Student1: (80-80)/(90-80) = 0/10 = 0.0
        // Student2: (90-80)/(90-80) = 10/10 = 1.0
        $this->assertEquals(0.0, $score1, 'Student with lower score should have 0.0');
        $this->assertEquals(1.0, $score2, 'Student with higher score should have 1.0');
    }

    /**
     * Test calculation with single cost criterion
     */
    public function testSingleCostCriterionWithRealData()
    {
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'tuition_payment_delays',
                    'student_attribute' => 'tuition_payment_delays',
                    'name' => 'Payment Delays',
                    'weight' => 1.0,
                    'type' => 'cost',
                    'data_type' => 'numeric',
                    'min_value' => 0,
                    'max_value' => 10
                ]
            ]
        ]);

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create students with different delay counts
        $student1 = Student::factory()->create(['tuition_payment_delays' => 1]);
        $student2 = Student::factory()->create(['tuition_payment_delays' => 3]);

        // Create submissions
        \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['tuition_payment_delays' => 1]
        ]);

        \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['tuition_payment_delays' => 3]
        ]);

        $score1 = $this->sawCalculatorService->calculateScore($student1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($student2, $batch);

        // Expected for cost criteria: (max - value) / (max - min)
        // Student1: (3-1)/(3-1) = 2/2 = 1.0 (fewer delays = better)
        // Student2: (3-3)/(3-1) = 0/2 = 0.0 (more delays = worse)
        $this->assertEquals(1.0, $score1, 'Student with fewer delays should have score 1.0');
        $this->assertEquals(0.0, $score2, 'Student with more delays should have score 0.0');
    }

    /**
     * Test edge case where min equals max for benefit criterion
     */
    public function testMinEqualsMaxBenefitCriterion()
    {
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

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // All students have the same score
        $student = Student::factory()->create(['average_score' => 85.0]);

        \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['average_score' => 85.0]
        ]);

        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        // When min = max and value > 0, service should return 1.0
        $this->assertEquals(1.0, $score, 'When min=max for benefit criterion and value > 0, score should be 1.0');
    }

    /**
     * Test edge case where min equals max and both are zero for benefit criterion
     */
    public function testMinEqualsMaxZeroBenefitCriterion()
    {
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

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Student with zero score
        $student = Student::factory()->create(['average_score' => 0.0]);

        \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['average_score' => 0.0]
        ]);

        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        // When min = max = 0 for benefit criterion, service should return 0.0
        $this->assertEquals(0.0, $score, 'When min=max=0 for benefit criterion, score should be 0.0');
    }

    /**
     * Test combined benefit and cost criteria
     */
    public function testCombinedBenefitAndCostCriteria()
    {
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'average_score',
                    'student_attribute' => 'average_score',
                    'name' => 'Average Score',
                    'weight' => 0.6,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                    'min_value' => 0,
                    'max_value' => 100
                ],
                [
                    'id' => 'tuition_payment_delays',
                    'student_attribute' => 'tuition_payment_delays',
                    'name' => 'Payment Delays',
                    'weight' => 0.4,
                    'type' => 'cost',
                    'data_type' => 'numeric',
                    'min_value' => 0,
                    'max_value' => 10
                ]
            ]
        ]);

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create students with known values
        $student = Student::factory()->create([
            'average_score' => 85.0,
            'tuition_payment_delays' => 2
        ]);

        $studentMin = Student::factory()->create([
            'average_score' => 70.0,
            'tuition_payment_delays' => 1
        ]);

        $studentMax = Student::factory()->create([
            'average_score' => 90.0,
            'tuition_payment_delays' => 3
        ]);

        // Create submissions for all students
        foreach ([$student, $studentMin, $studentMax] as $s) {
            \App\Models\StudentSubmission::factory()->create([
                'student_id' => $s->id,
                'scholarship_batch_id' => $batch->id,
                'submitted_by_teacher_id' => $teacher->id,
                'raw_criteria_values' => [
                    'average_score' => $s->average_score,
                    'tuition_payment_delays' => $s->tuition_payment_delays
                ]
            ]);
        }

        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        // Average Score (benefit): (85-70)/(90-70) = 15/20 = 0.75, weighted: 0.75 * 0.6 = 0.45
        // Delays (cost): (3-2)/(3-1) = 1/2 = 0.5, weighted: 0.5 * 0.4 = 0.2
        // Total: 0.45 + 0.2 = 0.65
        $this->assertEquals(0.65, $score, 'Combined score calculation should be correct');
    }
}
