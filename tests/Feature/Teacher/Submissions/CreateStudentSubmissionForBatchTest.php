<?php

namespace Tests\Feature\Teacher\Submissions;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\ScholarshipBatch;
use App\Models\StudentSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Teacher\Submissions\CreateStudentSubmissionForBatch;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class CreateStudentSubmissionForBatchTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected User $otherTeacher;
    protected User $admin;
    protected ScholarshipBatch $openBatch;
    protected ScholarshipBatch $closedBatch;
    protected Student $student1;
    protected Student $student2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'admin']);

        // Create users
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        $this->otherTeacher = User::factory()->create();
        $this->otherTeacher->assignRole('teacher');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');        // Create scholarship batches
        $this->openBatch = ScholarshipBatch::factory()->create([
            'status' => 'open',
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::tomorrow(),
            'criteria_config' => [
                [
                    'id' => 'average_score',
                    'name' => 'Average Score',
                    'student_model_key' => 'average_score',
                    'weight' => 0.6,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                    'min_value' => 0,
                    'max_value' => 100,
                    'rules' => 'required|numeric|min:0|max:100'
                ],
                [
                    'id' => 'attendance',
                    'name' => 'Class Attendance',
                    'student_model_key' => 'class_attendance_percentage',
                    'weight' => 0.4,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                    'min_value' => 0,
                    'max_value' => 100,
                    'rules' => 'required|numeric|min:0|max:100'
                ]
            ]
        ]);

        $this->closedBatch = ScholarshipBatch::factory()->create([
            'status' => 'closed',
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::yesterday(),
        ]);

        // Create students
        $this->student1 = Student::factory()->create([
            'teacher_id' => $this->teacher->id,
            'average_score' => 85.0,
            'class_attendance_percentage' => 90.0
        ]);

        $this->student2 = Student::factory()->create([
            'teacher_id' => $this->teacher->id,
            'average_score' => 75.0,
            'class_attendance_percentage' => 85.0
        ]);
    }    /** @test */
    public function teacher_can_access_submission_page_for_open_batch()
    {
        $this->actingAs($this->teacher)
            ->get(route('teacher.submissions.create-for-batch', $this->openBatch))
            ->assertOk()
            ->assertSeeLivewire(CreateStudentSubmissionForBatch::class);
    }

    /** @test */
    public function guest_cannot_access_submission_page()
    {
        $this->get(route('teacher.submissions.create-for-batch', $this->openBatch))
            ->assertRedirect(route('login'));
    }    /** @test */
    public function can_successfully_submit_students()
    {
        $this->assertEquals(0, StudentSubmission::count());

        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('selectedStudentIds', [$this->student1->id])
            ->set('studentCriteriaValues', [
                $this->student1->id => [
                    'average_score' => 85,
                    'attendance' => 90
                ]
            ])
            ->call('saveSubmission');

        $this->assertEquals(1, StudentSubmission::count());

        $submission = StudentSubmission::first();
        $this->assertEquals($this->student1->id, $submission->student_id);
        $this->assertEquals($this->openBatch->id, $submission->scholarship_batch_id);
        $this->assertEquals($this->teacher->id, $submission->submitted_by_teacher_id);
        $this->assertEquals('pending', $submission->status);
        $this->assertEquals(['average_score' => 85, 'attendance' => 90], $submission->raw_criteria_values);
    }    /** @test */
    public function validation_requires_at_least_one_student()
    {
        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->call('saveSubmission')
            ->assertHasErrors(['selectedStudentIds' => 'required']);
    }    /** @test */
    public function teacher_can_only_see_their_own_students_in_component()
    {
        $otherTeacherStudent = Student::factory()->create([
            'teacher_id' => $this->otherTeacher->id,
            'name' => 'Other Teacher Student',
            'average_score' => 95.0,
            'class_attendance_percentage' => 100.0
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch]);

        // Should see own students
        $component->assertSee($this->student1->name);
        $component->assertSee($this->student2->name);

        // Should not see other teacher's students in the rendered list
        $component->assertDontSee('Other Teacher Student');
    }

    /** @test */
    public function can_submit_multiple_students_at_once()
    {
        $student3 = Student::factory()->create([
            'teacher_id' => $this->teacher->id,
            'average_score' => 95.0,
            'class_attendance_percentage' => 88.0
        ]);

        $this->assertEquals(0, StudentSubmission::count());

        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('selectedStudentIds', [$this->student1->id, $this->student2->id, $student3->id])
            ->set('studentCriteriaValues', [
                $this->student1->id => [
                    'average_score' => 85,
                    'attendance' => 90
                ],
                $this->student2->id => [
                    'average_score' => 75,
                    'attendance' => 85
                ],
                $student3->id => [
                    'average_score' => 95,
                    'attendance' => 88
                ]
            ])
            ->call('saveSubmission');

        $this->assertEquals(3, StudentSubmission::count());

        // Verify all submissions are created correctly
        $submissions = StudentSubmission::all();
        $studentIds = $submissions->pluck('student_id')->toArray();
        $this->assertContains($this->student1->id, $studentIds);
        $this->assertContains($this->student2->id, $studentIds);
        $this->assertContains($student3->id, $studentIds);
    }

    /** @test */
    public function cannot_submit_same_student_twice_for_same_batch()
    {
        // First submission
        StudentSubmission::factory()->create([
            'student_id' => $this->student1->id,
            'scholarship_batch_id' => $this->openBatch->id,
            'submitted_by_teacher_id' => $this->teacher->id,
            'raw_criteria_values' => ['average_score' => 80, 'attendance' => 85]
        ]);

        $this->assertEquals(1, StudentSubmission::count());

        // Attempt to submit the same student again
        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('selectedStudentIds', [$this->student1->id])
            ->set('studentCriteriaValues', [
                $this->student1->id => [
                    'average_score' => 85,
                    'attendance' => 90
                ]
            ])
            ->call('saveSubmission');

        // Should still have only one submission
        $this->assertEquals(1, StudentSubmission::count());
    }

    /** @test */
    public function can_submit_student_for_different_batches()
    {
        $secondBatch = ScholarshipBatch::factory()->create([
            'status' => 'open',
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::tomorrow(),
            'criteria_config' => $this->openBatch->criteria_config
        ]);

        // Submit to first batch
        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('selectedStudentIds', [$this->student1->id])
            ->set('studentCriteriaValues', [
                $this->student1->id => [
                    'average_score' => 85,
                    'attendance' => 90
                ]
            ])
            ->call('saveSubmission');

        // Submit same student to second batch
        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $secondBatch])
            ->set('selectedStudentIds', [$this->student1->id])
            ->set('studentCriteriaValues', [
                $this->student1->id => [
                    'average_score' => 88,
                    'attendance' => 92
                ]
            ])
            ->call('saveSubmission');

        $this->assertEquals(2, StudentSubmission::count());

        // Verify submissions are for different batches
        $submissions = StudentSubmission::where('student_id', $this->student1->id)->get();
        $batchIds = $submissions->pluck('scholarship_batch_id')->toArray();
        $this->assertContains($this->openBatch->id, $batchIds);
        $this->assertContains($secondBatch->id, $batchIds);
    }

    /** @test */
    public function cannot_access_closed_batch_submission_page()
    {
        $this->actingAs($this->teacher)
            ->get(route('teacher.submissions.create-for-batch', $this->closedBatch))
            ->assertRedirect();
    }    /** @test */
    public function cannot_submit_to_closed_batch()
    {
        $this->assertEquals(0, StudentSubmission::count());

        // The component should handle closed batch gracefully
        // Since closed batch access should be prevented, we just verify no submissions are created
        try {
            Livewire::actingAs($this->teacher)
                ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->closedBatch])
                ->set('selectedStudentIds', [$this->student1->id])
                ->set('studentCriteriaValues', [
                    $this->student1->id => [
                        'average_score' => 85,
                        'attendance' => 90
                    ]
                ])
                ->call('saveSubmission');
        } catch (\Exception $e) {
            // Expected - component may prevent access to closed batch
        }

        // Verify no submissions were created regardless of how the component handles closed batch
        $this->assertEquals(0, StudentSubmission::count());
    }

    /** @test */
    public function validation_requires_all_criteria_values()
    {
        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('selectedStudentIds', [$this->student1->id])
            ->set('studentCriteriaValues', [
                $this->student1->id => [
                    'average_score' => 85,
                    // Missing 'attendance' field
                ]
            ])
            ->call('saveSubmission')
            ->assertHasErrors(['studentCriteriaValues.' . $this->student1->id . '.attendance']);
    }

    /** @test */
    public function validates_numeric_criteria_within_range()
    {
        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('selectedStudentIds', [$this->student1->id])
            ->set('studentCriteriaValues', [
                $this->student1->id => [
                    'average_score' => 150, // Above max of 100
                    'attendance' => -10,    // Below min of 0
                ]
            ])
            ->call('saveSubmission')
            ->assertHasErrors([
                'studentCriteriaValues.' . $this->student1->id . '.average_score',
                'studentCriteriaValues.' . $this->student1->id . '.attendance'
            ]);
    }

    /** @test */
    public function search_functionality_works_correctly()
    {
        $searchableStudent = Student::factory()->create([
            'teacher_id' => $this->teacher->id,
            'name' => 'John Searchable',
            'nisn' => '1111111111'
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('studentSearch', 'Searchable');

        // Check that the component contains the searchable student
        $component->assertSee('John Searchable');
        $component->assertSee('1111111111');

        // Should not see other students when searching
        $component->assertDontSee($this->student1->name);
        $component->assertDontSee($this->student2->name);
    }

    /** @test */
    public function component_loads_teacher_students_only()
    {
        $otherTeacherStudent = Student::factory()->create([
            'teacher_id' => $this->otherTeacher->id,
            'name' => 'Other Teacher Student'
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch]);

        // Should see own students
        $component->assertSee($this->student1->name);
        $component->assertSee($this->student2->name);

        // Should not see other teacher's students
        $component->assertDontSee('Other Teacher Student');
    }    /** @test */
    public function criteria_values_are_initialized_when_student_selected()
    {
        $component = Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch]);

        // Initially no criteria values should be set
        $this->assertEmpty($component->get('studentCriteriaValues'));

        // Setting selectedStudentIds should trigger the lifecycle hook automatically
        $component->set('selectedStudentIds', [$this->student1->id]);

        // Check that component has some criteria values initialized for the student
        $criteriaValues = $component->get('studentCriteriaValues.' . $this->student1->id);
        $this->assertNotNull($criteriaValues);
        $this->assertIsArray($criteriaValues);

        // Should have keys for both criteria
        $this->assertArrayHasKey('average_score', $criteriaValues);
        $this->assertArrayHasKey('attendance', $criteriaValues);
    }/** @test */
    public function can_deselect_student_using_deselect_method()
    {
        $component = Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('selectedStudentIds', [$this->student1->id, $this->student2->id]);

        // Verify both students are selected
        $component->assertSet('selectedStudentIds', [$this->student1->id, $this->student2->id]);

        // Deselect one student
        $component->call('deselectStudent', $this->student1->id);

        // Verify only one student remains selected
        $component->assertSet('selectedStudentIds', [$this->student2->id]);

        // Verify criteria values for deselected student are removed
        $criteriaExists = $component->get('studentCriteriaValues.' . $this->student1->id);
        $this->assertNull($criteriaExists);
    }

    /** @test */
    public function admin_cannot_access_teacher_submission_page()
    {
        $this->actingAs($this->admin)
            ->get(route('teacher.submissions.create-for-batch', $this->openBatch))
            ->assertStatus(403);
    }

    /** @test */
    public function submission_includes_correct_metadata()
    {
        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->openBatch])
            ->set('selectedStudentIds', [$this->student1->id])
            ->set('studentCriteriaValues', [
                $this->student1->id => [
                    'average_score' => 85,
                    'attendance' => 90
                ]
            ])
            ->call('saveSubmission');

        $submission = StudentSubmission::first();

        // Verify submission metadata
        $this->assertEquals($this->student1->id, $submission->student_id);
        $this->assertEquals($this->openBatch->id, $submission->scholarship_batch_id);
        $this->assertEquals($this->teacher->id, $submission->submitted_by_teacher_id);
        $this->assertEquals('pending', $submission->status);
        $this->assertNotNull($submission->submission_date);

        // Verify raw criteria values structure matches criteria config
        $this->assertArrayHasKey('average_score', $submission->raw_criteria_values);
        $this->assertArrayHasKey('attendance', $submission->raw_criteria_values);
        $this->assertEquals(85, $submission->raw_criteria_values['average_score']);
        $this->assertEquals(90, $submission->raw_criteria_values['attendance']);
    }
}
