<?php

namespace Tests\Unit;

use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\StudentSubmission;
use App\Models\User;
use App\Services\SAWCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role; // Import Role model

uses(RefreshDatabase::class);

it('calculates SAW scores correctly for a batch', function () {
    // 0. Create roles if they don't exist (important for spatie/laravel-permission)
    Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']); // Assuming students might also have a role

    // 1. Arrange
    $criterion1Id = Str::uuid()->toString();
    $criterion2Id = Str::uuid()->toString();
    $criterion3Id = Str::uuid()->toString();

    $batch = ScholarshipBatch::factory()->create([
        'criteria_config' => [
            ['id' => $criterion1Id, 'name' => 'GPA', 'weight' => 0.4, 'type' => 'benefit', 'data_type' => 'numeric'],
            ['id' => $criterion2Id, 'name' => 'Income', 'weight' => 0.3, 'type' => 'cost', 'data_type' => 'numeric'],
            ['id' => $criterion3Id, 'name' => 'Dependents', 'weight' => 0.3, 'type' => 'benefit', 'data_type' => 'numeric'],
        ]
    ]);

    // Create a teacher for the student
    $teacherForStudent = User::factory()->create();
    $teacherForStudent->assignRole('teacher');

    // Create a student
    $student = Student::factory()->create([
        'teacher_id' => $teacherForStudent->id,
        'name' => 'Test Student',
    ]);

    // Create a teacher who submits the submission
    $submittingTeacher = User::factory()->create();
    $submittingTeacher->assignRole('teacher');

    $submission = StudentSubmission::factory()->create([
        'scholarship_batch_id' => $batch->id,
        'student_id' => $student->id,
        'submitted_by_teacher_id' => $submittingTeacher->id,
        'raw_criteria_values' => [
            $criterion1Id => 3.5, // GPA
            $criterion2Id => 5000000, // Income
            $criterion3Id => 3,       // Dependents
        ],
        'normalized_scores' => [],
        'final_saw_score' => 0.0,
        'status' => 'pending',
    ]);

    $service = new SAWCalculatorService();
    $updatedSubmissions = $service->calculateScoresForBatch($batch, collect([$submission]));
    $updatedSubmission = $updatedSubmissions->first();

    // Persist the changes to the database
    $updatedSubmission->save();

    // 3. Assert
    expect($updatedSubmission->normalized_scores)->toBeArray()->not->toBeEmpty();
    expect($updatedSubmission->final_saw_score)->toBeNumeric();

    expect($updatedSubmission->normalized_scores[$criterion1Id])->toEqual(1.0); // GPA (benefit)
    expect($updatedSubmission->normalized_scores[$criterion2Id])->toEqual(1.0); // Income (cost)
    expect($updatedSubmission->normalized_scores[$criterion3Id])->toEqual(1.0); // Dependents (benefit)

    expect($updatedSubmission->final_saw_score)->toEqual(1.0);

    $this->assertDatabaseHas('student_submissions', [
        'id' => $submission->id,
        'final_saw_score' => 1.0,
    ]);

    $dbSubmission = StudentSubmission::find($submission->id);
    expect($dbSubmission->normalized_scores[$criterion1Id])->toEqual(1.0);
    expect($dbSubmission->normalized_scores[$criterion2Id])->toEqual(1.0);
    expect($dbSubmission->normalized_scores[$criterion3Id])->toEqual(1.0);
});
