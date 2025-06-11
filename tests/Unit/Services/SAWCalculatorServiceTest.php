<?php

namespace Tests\Unit\Services;

use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentSubmission; // Added this line
use App\Services\SAWCalculatorService;
use App\Services\PredefinedCriteriaService;
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
        $predefinedCriteriaService = new PredefinedCriteriaService();
        $this->sawCalculatorService = new SAWCalculatorService($predefinedCriteriaService);

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
                    'id' => 'test_average_score', 
                    'name' => 'Average Score',
                    'weight' => 1.0,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                ]
            ]
        ]);

        // Create a teacher
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create students 
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();
        $student3 = Student::factory()->create(); 

        // Create submissions for normalization
        $submission1 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_average_score' => 70.0] 
        ]);

        $submission2 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_average_score' => 80.0] 
        ]);

        $submission3 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student3->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_average_score' => 90.0] 
        ]);
        
        $score1 = $this->sawCalculatorService->calculateScore($submission1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($submission2, $batch);
        $score3 = $this->sawCalculatorService->calculateScore($submission3, $batch);
        
        $this->assertEquals(0.0, $score1, 'Student with min score should have 0.0');
        $this->assertEquals(0.5, $score2, 'Student with mid score should have 0.5');
        $this->assertEquals(1.0, $score3, 'Student with max score should have 1.0');
    }

    /**
     * Test calculation with single cost criterion
     */
    public function testSingleCostCriterionWithRealData()
    {
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'test_tuition_payment_delays', 
                    'name' => 'Payment Delays',
                    'weight' => 1.0,
                    'type' => 'cost',
                    'data_type' => 'numeric',
                ]
            ]
        ]);

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();
        $student3 = Student::factory()->create();

        $submission1 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_tuition_payment_delays' => 1] 
        ]);

        $submission2 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_tuition_payment_delays' => 2] 
        ]);
        
        $submission3 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student3->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_tuition_payment_delays' => 3] 
        ]);
        
        $score1 = $this->sawCalculatorService->calculateScore($submission1, $batch);
        $score2 = $this->sawCalculatorService->calculateScore($submission2, $batch);
        $score3 = $this->sawCalculatorService->calculateScore($submission3, $batch);

        $this->assertEquals(1.0, $score1, 'Student with fewest delays should have score 1.0');
        $this->assertEquals(0.5, $score2, 'Student with mid delays should have score 0.5');
        $this->assertEquals(0.0, $score3, 'Student with most delays should have score 0.0');
    }

    /**
     * Test edge case where min equals max for benefit criterion
     */
    public function testMinEqualsMaxBenefitCriterion()
    {
        $batch = ScholarshipBatch::factory()->create([
            'criteria_config' => [
                [
                    'id' => 'test_average_score', 
                    'name' => 'Average Score',
                    'weight' => 1.0,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                ]
            ]
        ]);

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        $submission1 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_average_score' => 85.0] 
        ]);
        $submission2 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_average_score' => 85.0]
        ]);

        $score = $this->sawCalculatorService->calculateScore($submission1, $batch);

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
                    'id' => 'test_average_score', 
                    'name' => 'Average Score',
                    'weight' => 1.0,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                ]
            ]
        ]);

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        $submission1 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student1->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_average_score' => 0.0] 
        ]);
        $submission2 = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $student2->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => ['test_average_score' => 0.0]
        ]);

        $score = $this->sawCalculatorService->calculateScore($submission1, $batch);

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
                    'id' => 'test_average_score', 
                    'name' => 'Average Score',
                    'weight' => 0.6,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                ],
                [
                    'id' => 'test_tuition_payment_delays', 
                    'name' => 'Payment Delays',
                    'weight' => 0.4,
                    'type' => 'cost',
                    'data_type' => 'numeric',
                ]
            ]
        ]);

        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $studentForTest = Student::factory()->create(); 
        $studentMinValues = Student::factory()->create(); 
        $studentMaxValues = Student::factory()->create(); 

        $submissionForTest = StudentSubmission::factory()->create([ // Corrected
            'student_id' => $studentForTest->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => [
                'test_average_score' => 85.0, 
                'test_tuition_payment_delays' => 2  
            ]
        ]);

        StudentSubmission::factory()->create([ // Corrected
            'student_id' => $studentMinValues->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => [
                'test_average_score' => 70.0, 
                'test_tuition_payment_delays' => 1  
            ]
        ]);

        StudentSubmission::factory()->create([ // Corrected
            'student_id' => $studentMaxValues->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $teacher->id,
            'raw_criteria_values' => [
                'test_average_score' => 90.0, 
                'test_tuition_payment_delays' => 3  
            ]
        ]);
        
        $score = $this->sawCalculatorService->calculateScore($submissionForTest, $batch);
        
        $this->assertEquals(0.65, $score, 'Combined score calculation should be correct');
    }
}
