<?php

namespace Tests\Unit\Services;

use App\Services\SAWCalculatorService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class SAWCalculatorServiceSimpleTest extends TestCase
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
    public function testEmptyCriteriaConfigReturnsZero()
    {
        // Create actual models using factories for integration testing
        $batch = \App\Models\ScholarshipBatch::factory()->create([
            'criteria_config' => []
        ]);

        $student = \App\Models\Student::factory()->create();

        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        $this->assertEquals(0.0, $score);
    }    /**
     * Test calculation with null criteria_config returns 0
     */
    public function testNullCriteriaConfigReturnsZero()
    {
        // Since criteria_config is not nullable in migration, test with empty array instead
        $batch = \App\Models\ScholarshipBatch::factory()->create([
            'criteria_config' => []
        ]);

        $student = \App\Models\Student::factory()->create();

        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        $this->assertEquals(0.0, $score);
    }    /**
     * Test basic functionality with one benefit criterion
     * This test creates actual database records and tests the full integration
     */
    public function testBasicBenefitCriterionCalculation()
    {
        // Create a scholarship batch with one benefit criterion using actual Student field
        $batch = \App\Models\ScholarshipBatch::factory()->create([
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

        // Create students with different average scores
        $student1 = \App\Models\Student::factory()->create(['average_score' => 85.0]);
        $student2 = \App\Models\Student::factory()->create(['average_score' => 95.0]);

        // Create teacher for submissions
        $teacher = \App\Models\User::factory()->create();
        $teacher->assignRole('teacher');

        // Create submissions for normalization
        \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['average_score' => 85.0]
        ]);

        \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['average_score' => 95.0]
        ]);

        // Calculate scores
        $score1 = $this->sawCalculatorService->calculateScore($student1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($student2, $batch);

        // Student1: (85-85)/(95-85) = 0/10 = 0.0
        // Student2: (95-85)/(95-85) = 10/10 = 1.0
        $this->assertEquals(0.0, $score1, 'Student 1 should have score 0.0');
        $this->assertEquals(1.0, $score2, 'Student 2 should have score 1.0');
    }    /**
     * Test basic functionality with one cost criterion
     */
    public function testBasicCostCriterionCalculation()
    {
        $batch = \App\Models\ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'tuition_payment_delays',
                    'student_attribute' => 'tuition_payment_delays',
                    'name' => 'Tuition Payment Delays',
                    'weight' => 1.0,
                    'type' => 'cost',
                    'data_type' => 'numeric',
                    'min_value' => 0,
                    'max_value' => 10
                ]
            ]
        ]);

        // Create students with different delay values (lower delays = better for scholarship)
        $student1 = \App\Models\Student::factory()->create(['tuition_payment_delays' => 1]);
        $student2 = \App\Models\Student::factory()->create(['tuition_payment_delays' => 3]);

        // Create teacher for submissions
        $teacher = \App\Models\User::factory()->create();
        $teacher->assignRole('teacher');

        // Create submissions with teacher assignment
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
        ]);        $score1 = $this->sawCalculatorService->calculateScore($student1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($student2, $batch);

        // For cost criteria: (max - value) / (max - min)
        // Student1: (3-1)/(3-1) = 2/2 = 1.0
        // Student2: (3-3)/(3-1) = 0/2 = 0.0
        $this->assertEquals(1.0, $score1, 'Student 1 (fewer delays) should have score 1.0');
        $this->assertEquals(0.0, $score2, 'Student 2 (more delays) should have score 0.0');
    }    /**
     * Test combined benefit and cost criteria
     */
    public function testCombinedBenefitAndCostCriteria()
    {
        $batch = \App\Models\ScholarshipBatch::factory()->create([
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
                    'name' => 'Tuition Payment Delays',
                    'weight' => 0.4,
                    'type' => 'cost',
                    'data_type' => 'numeric',
                    'min_value' => 0,
                    'max_value' => 10
                ]
            ]
        ]);

        // Create students
        $student = \App\Models\Student::factory()->create([
            'average_score' => 85.0,
            'tuition_payment_delays' => 2
        ]);

        $studentMin = \App\Models\Student::factory()->create([
            'average_score' => 70.0,
            'tuition_payment_delays' => 1
        ]);

        $studentMax = \App\Models\Student::factory()->create([
            'average_score' => 90.0,
            'tuition_payment_delays' => 3
        ]);

        // Create teacher for submissions
        $teacher = \App\Models\User::factory()->create();
        $teacher->assignRole('teacher');

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
        }        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        // Average Score (benefit): (85-70)/(90-70) = 15/20 = 0.75, weighted: 0.75 * 0.6 = 0.45
        // Delays (cost): (3-2)/(3-1) = 1/2 = 0.5, weighted: 0.5 * 0.4 = 0.2
        // Total: 0.45 + 0.2 = 0.65
        $this->assertEquals(0.65, $score, 'Combined score calculation should be correct');
    }    /**
     * Test edge case where min equals max for benefit criterion
     */
    public function testMinEqualsMaxBenefitCriterion()
    {
        $batch = \App\Models\ScholarshipBatch::factory()->create([
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

        // All students have the same average score
        $student = \App\Models\Student::factory()->create(['average_score' => 85.0]);

        // Create teacher for submissions
        $teacher = \App\Models\User::factory()->create();
        $teacher->assignRole('teacher');

        \App\Models\StudentSubmission::factory()->create([
            'student_id' => $student->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['average_score' => 85.0]
        ]);

        $score = $this->sawCalculatorService->calculateScore($student, $batch);

        // When min = max and value > 0, service should return 1.0
        $this->assertEquals(1.0, $score, 'When min=max for benefit criterion and value > 0, score should be 1.0');
    }    /**
     * Test edge case where min equals max and both are zero for benefit criterion
     */
    public function testMinEqualsMaxZeroBenefitCriterion()
    {
        $batch = \App\Models\ScholarshipBatch::factory()->create([
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

        $student = \App\Models\Student::factory()->create(['average_score' => 0.0]);

        // Create teacher for submissions
        $teacher = \App\Models\User::factory()->create();
        $teacher->assignRole('teacher');

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
}
