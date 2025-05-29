<?php

namespace Tests\Feature\Admin\Results;

use App\Livewire\Admin\Results\ScholarshipResults;
use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\StudentSubmission;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->admin = User::role('admin')->first();
    $this->teacher = User::role('teacher')->first();

    $this->student1 = Student::factory()->create(['teacher_id' => $this->teacher->id]);
    $this->student2 = Student::factory()->create(['teacher_id' => $this->teacher->id]);
    $this->student3 = Student::factory()->create(['teacher_id' => $this->teacher->id]);

    $this->batch = ScholarshipBatch::factory()->create([
        'name' => 'Test Scholarship Batch',
        'quota' => 2,
        'criteria_config' => [
            [
                'name' => 'Academic Score',
                'data_type' => 'numeric',
                'weight' => 0.6,
                'type' => 'benefit',
                'min_value' => 0,
                'max_value' => 100
            ],
            [
                'name' => 'Income',
                'data_type' => 'numeric',
                'weight' => 0.4,
                'type' => 'cost',
                'min_value' => 1000000,
                'max_value' => 10000000
            ]
        ]
    ]);
});

test('admin can access results page', function () {
    $this->actingAs($this->admin);

    $response = $this->get(route('admin.scholarship-batches.results', $this->batch));

    $response->assertSuccessful();
    $response->assertSeeLivewire(ScholarshipResults::class);
});

test('non admin cannot access results page', function () {
    $this->actingAs($this->teacher);

    $response = $this->get(route('admin.scholarship-batches.results', $this->batch));

    $response->assertStatus(403);
});

test('guest cannot access results page', function () {
    $response = $this->get(route('admin.scholarship-batches.results', $this->batch));

    $response->assertRedirect(route('login'));
});

test('component displays correct statistics', function () {
    // Create submissions with different statuses
    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'approved',
        'final_saw_score' => 0.85,        'raw_criteria_values' => [
            'Academic Score' => 85,
            'Income' => 2000000
        ]
    ]);

    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student2->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'rejected',
        'final_saw_score' => 0.45,
        'raw_criteria_values' => [
            'Academic Score' => 60,
            'Income' => 8000000
        ]    ]);

    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student3->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'pending',
        'final_saw_score' => 0.75,
        'raw_criteria_values' => [
            'Academic Score' => 78,
            'Income' => 3000000
        ]
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->assertSet('totalSubmissions', 3)
        ->assertSet('approvedCount', 1)
        ->assertSet('rejectedCount', 1)
        ->assertSet('pendingCount', 1)
        ->assertSet('quota', 2)
        ->assertSet('remainingSlots', 1)
        ->assertSet('averageScore', 0.6833)
        ->assertSet('highestScore', 0.85)
        ->assertSet('lowestScore', 0.45);
});

test('auto approve top candidates works', function () {
    // Create submissions with scores
    $submission1 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'pending',
        'final_saw_score' => 0.95,        'raw_criteria_values' => [
            'Academic Score' => 95,
            'Income' => 2000000
        ]
    ]);

    $submission2 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student2->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'pending',
        'final_saw_score' => 0.85,
        'raw_criteria_values' => [
            'Academic Score' => 85,
            'Income' => 3000000
        ]
    ]);

    $submission3 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student3->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'pending',
        'final_saw_score' => 0.75,
        'raw_criteria_values' => [
            'Academic Score' => 75,
            'Income' => 4000000
        ]
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->call('autoApproveTopCandidates')
        ->assertHasNoErrors();

    // Check that top 2 candidates (based on quota) were approved
    expect($submission1->fresh()->status)->toBe('approved');
    expect($submission2->fresh()->status)->toBe('approved');
    expect($submission3->fresh()->status)->toBe('pending');
});

test('bulk approve selected submissions', function () {
    $submission1 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'pending',
        'raw_criteria_values' => [
            'Academic Score' => 80,
            'Income' => 2500000
        ]
    ]);

    $submission2 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student2->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'pending',
        'raw_criteria_values' => [
            'Academic Score' => 85,
            'Income' => 3000000
        ]
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->set('selectedStudentIds', [$submission1->id, $submission2->id])
        ->call('bulkApprove')
        ->assertHasNoErrors();

    expect($submission1->fresh()->status)->toBe('approved');
    expect($submission2->fresh()->status)->toBe('approved');
});

test('bulk reject selected submissions', function () {    $submission1 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'pending',
        'raw_criteria_values' => [
            'Academic Score' => 80,
            'Income' => 2500000
        ]
    ]);

    $submission2 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student2->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'pending',
        'raw_criteria_values' => [
            'Academic Score' => 75,
            'Income' => 3000000
        ]
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->set('selectedStudentIds', [$submission1->id, $submission2->id])
        ->call('bulkReject')
        ->assertHasNoErrors();

    expect($submission1->fresh()->status)->toBe('rejected');
    expect($submission2->fresh()->status)->toBe('rejected');
});

test('refresh scores recalculates all submissions', function () {
    $submission = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'final_saw_score' => 0.50, // Old score
        'raw_criteria_values' => [
            'Academic Score' => 90,
            'Income' => 2000000
        ]
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->call('refreshScores')
        ->assertHasNoErrors();

    // Score should be recalculated (new score should be different)
    expect($submission->fresh()->final_saw_score)->not->toBe(0.50);
});

test('status filter works', function () {    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'approved',
        'raw_criteria_values' => [
            'Academic Score' => 90,
            'Income' => 1500000
        ]
    ]);

    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student2->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'status' => 'rejected',
        'raw_criteria_values' => [
            'Academic Score' => 70,
            'Income' => 4000000
        ]
    ]);

    $this->actingAs($this->admin);

    // Test approved filter
    $component = Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->set('statusFilter', 'approved');

    $submissions = $component->viewData('submissions');
    expect($submissions->total())->toBe(1);
    expect($submissions->first()->status)->toBe('approved');

    // Test rejected filter
    $component = Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->set('statusFilter', 'rejected');

    $submissions = $component->viewData('submissions');
    expect($submissions->total())->toBe(1);
    expect($submissions->first()->status)->toBe('rejected');
});

test('rankings are calculated correctly', function () {    // Create submissions with different scores
    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'final_saw_score' => 0.95,
        'raw_criteria_values' => [
            'Academic Score' => 95,
            'Income' => 1000000
        ]
    ]);

    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student2->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'final_saw_score' => 0.85,
        'raw_criteria_values' => [
            'Academic Score' => 85,
            'Income' => 2000000
        ]
    ]);    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student3->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'final_saw_score' => 0.75,
        'raw_criteria_values' => [
            'Academic Score' => 75,
            'Income' => 3000000
        ]
    ]);

    $this->actingAs($this->admin);

    $component = Livewire::test(ScholarshipResults::class, ['batch' => $this->batch]);
    $submissions = $component->viewData('submissions');

    // Check rankings are in correct order (highest score first)
    $submissionArray = $submissions->items();
    expect($submissionArray[0]->final_saw_score)->toBe(0.95);
    expect($submissionArray[1]->final_saw_score)->toBe(0.85);
    expect($submissionArray[2]->final_saw_score)->toBe(0.75);
});

test('toggle selection works', function () {
    $submission = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'raw_criteria_values' => [
            'Academic Score' => 85,
            'Income' => 2500000
        ]
    ]);

    $this->actingAs($this->admin);

    $component = Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->call('toggleSelection', $submission->id)
        ->assertSet('selectedStudentIds', [$submission->id]);

    // Toggle again to deselect
    $component->call('toggleSelection', $submission->id)
        ->assertSet('selectedStudentIds', []);
});

test('select all and clear selection works', function () {
    $submission1 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student1->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'raw_criteria_values' => [
            'Academic Score' => 85,
            'Income' => 2500000
        ]
    ]);

    $submission2 = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->batch->id,
        'student_id' => $this->student2->id,
        'submitted_by_teacher_id' => $this->teacher->id,
        'raw_criteria_values' => [
            'Academic Score' => 75,
            'Income' => 3000000
        ]
    ]);

    $this->actingAs($this->admin);

    $component = Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->call('selectAllOnPage');

    $selectedIds = $component->get('selectedStudentIds');
    expect($selectedIds)->toContain($submission1->id);
    expect($selectedIds)->toContain($submission2->id);

    // Clear selection
    $component->call('clearSelection')
        ->assertSet('selectedStudentIds', []);
});

test('component handles empty submissions', function () {
    $this->actingAs($this->admin);

    Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->assertSet('totalSubmissions', 0)
        ->assertSet('approvedCount', 0)
        ->assertSet('rejectedCount', 0)
        ->assertSet('pendingCount', 0)
        ->assertSet('averageScore', 0)
        ->assertSet('highestScore', 0)
        ->assertSet('lowestScore', 0);
});

test('quota enforcement in auto approve', function () {
    // Create more submissions than quota
    for ($i = 1; $i <= 5; $i++) {
        $student = Student::factory()->create(['teacher_id' => $this->teacher->id]);
        StudentSubmission::factory()->create([
            'scholarship_batch_id' => $this->batch->id,
            'student_id' => $student->id,
            'submitted_by_teacher_id' => $this->teacher->id,
            'status' => 'pending',
            'final_saw_score' => 0.9 - ($i * 0.1), // Decreasing scores
            'raw_criteria_values' => [
                'Academic Score' => 90 - ($i * 5),
                'Income' => 1500000 + ($i * 500000)
            ]
        ]);
    }

    $this->actingAs($this->admin);

    Livewire::test(ScholarshipResults::class, ['batch' => $this->batch])
        ->call('autoApproveTopCandidates')
        ->assertHasNoErrors();

    // Only quota number should be approved (2 in this case)
    $approvedCount = StudentSubmission::where('scholarship_batch_id', $this->batch->id)
        ->where('status', 'approved')
        ->count();

    expect($approvedCount)->toBe(2);
});
