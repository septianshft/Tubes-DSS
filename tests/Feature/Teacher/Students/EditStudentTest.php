<?php

use App\Livewire\Teacher\Students\EditStudent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create roles first
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'teacher']);
    Role::create(['name' => 'student']);

    // Create teachers
    $this->teacher = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Test Teacher',
        'email' => 'teacher@test.com'
    ]);
    $this->teacher->assignRole('teacher');

    $this->otherTeacher = User::factory()->create([
        'role' => 'teacher',
        'name' => 'Other Teacher',
        'email' => 'other@test.com'
    ]);
    $this->otherTeacher->assignRole('teacher');

    // Create students
    $this->student = Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'Test Student',
        'nisn' => '1234567890',
        'email' => 'student@test.com',
        'phone' => '081234567890',
        'address' => 'Test Address'
    ]);

    $this->otherTeacherStudent = Student::factory()->create([
        'teacher_id' => $this->otherTeacher->id,
        'name' => 'Other Student',
        'nisn' => '0987654321',
        'email' => 'other.student@test.com'
    ]);
});

test('teacher can access edit page for their student', function () {
    $this->actingAs($this->teacher);

    $response = $this->get(route('teacher.students.edit', $this->student));

    $response->assertOk();
    $response->assertSee('Edit Student');
    $response->assertSee($this->student->name);
    $response->assertSeeHtml('wire:submit.prevent="update"'); // Changed to assertSeeHtml for more precise matching
});

test('teacher cannot access edit page for other teachers student', function () {
    $this->actingAs($this->teacher);

    $response = $this->get(route('students.edit', $this->otherTeacherStudent));

    // Should redirect or show 403
    $response->assertStatus(403);
});

test('guest cannot access edit page', function () {
    $response = $this->get(route('students.edit', $this->student));

    $response->assertRedirect(route('login'));
});

test('admin cannot access teacher edit student page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get(route('students.edit', $this->student));

    $response->assertStatus(403);
});

test('edit student component loads with correct data', function () {
    $this->actingAs($this->teacher);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->assertSet('name', $this->student->name)
        ->assertSet('nisn', $this->student->nisn)
        ->assertSet('email', $this->student->email)
        ->assertSet('phone', $this->student->phone)
        ->assertSet('address', $this->student->address)
        ->assertSee($this->student->name)
        ->assertSee($this->student->nisn);
});

test('teacher can update their student', function () {
    $this->actingAs($this->teacher);

    $newData = [
        'name' => 'Updated Student Name',
        'nisn' => '1111111111',
        'email' => 'updated@test.com',
        'phone' => '081111111111',
        'address' => 'Updated Address'
    ];

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('name', $newData['name'])
        ->set('nisn', $newData['nisn'])
        ->set('email', $newData['email'])
        ->set('phone', $newData['phone'])
        ->set('address', $newData['address'])
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('teacher.students.index'));

    // Verify the student was updated in the database
    $this->student->refresh();
    expect($this->student->name)->toBe($newData['name']);
    expect($this->student->nisn)->toBe($newData['nisn']);
    expect($this->student->email)->toBe($newData['email']);
    expect($this->student->phone)->toBe($newData['phone']);
    expect($this->student->address)->toBe($newData['address']);
});

test('teacher cannot update other teachers student', function () {
    $actingTeacher = $this->teacher;
    $otherTeacher = User::factory()->state(['role' => 'teacher'])->create();
    $studentOfOtherTeacher = Student::factory()->state(['teacher_id' => $otherTeacher->id])->create();
    $originalName = $studentOfOtherTeacher->name; // Store original name
    $originalEmail = $studentOfOtherTeacher->email; // Store other original data if necessary

    // Test that mounting the component (and thus attempting to edit)
    // with another teacher's student is forbidden.
    // This implicitly protects the update action as well, since the component
    // cannot be properly initialized to receive an update call if mount is forbidden.
    Livewire::actingAs($actingTeacher)
        ->test(EditStudent::class, ['student' => $studentOfOtherTeacher])
        ->assertForbidden();

    // Verify student data remains unchanged, as the component interaction was forbidden.
    $studentOfOtherTeacher->refresh(); // Refresh from DB
    $this->assertDatabaseHas('students', [
        'id' => $studentOfOtherTeacher->id,
        'name' => $originalName, // Check against the original name
        'email' => $originalEmail, // Check against original email
    ]);
    // Also, explicitly check that the name wasn't changed to a hypothetical "Attempted Update Name"
    // This is covered by checking against originalName but adds clarity.
    expect($studentOfOtherTeacher->name)->toBe($originalName);
    expect($studentOfOtherTeacher->name)->not->toBe('Attempted Update Name');
});

test('validation errors for required fields', function () {
    $this->actingAs($this->teacher);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('name', '')
        ->set('nisn', '')
        ->set('email', '')
        ->call('update')
        ->assertHasErrors([
            'name' => 'required',
            'nisn' => 'required',
            'email' => 'required'
        ]);
});

test('validation error for invalid email', function () {
    $this->actingAs($this->teacher);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('email', 'invalid-email')
        ->call('update')
        ->assertHasErrors(['email' => 'email']);
});

test('validation error for duplicate nisn different student', function () {
    $this->actingAs($this->teacher);

    // Create another student with a different NISN
    $anotherStudent = Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'nisn' => '5555555555'
    ]);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('nisn', '5555555555') // Try to use the other student's NISN
        ->call('update')
        ->assertHasErrors(['nisn' => 'unique']);
});

test('can keep same nisn when updating other fields', function () {
    $this->actingAs($this->teacher);

    $originalNisn = $this->student->nisn;

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('name', 'Updated Name Only')
        ->set('nisn', $originalNisn) // Keep the same NISN
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('teacher.students.index'));

    $this->student->refresh();
    expect($this->student->name)->toBe('Updated Name Only');
    expect($this->student->nisn)->toBe($originalNisn);
});

test('validation error for duplicate email different student', function () {
    $this->actingAs($this->teacher);

    // Create another student with a different email
    $anotherStudent = Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'email' => 'another@test.com'
    ]);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('email', 'another@test.com') // Try to use the other student's email
        ->call('update')
        ->assertHasErrors(['email' => 'unique']);
});

test('nisn length validation', function () {
    $this->actingAs($this->teacher);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('nisn', '123') // Too short
        ->call('update')
        ->assertHasErrors(['nisn']);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('nisn', '12345678901') // Too long
        ->call('update')
        ->assertHasErrors(['nisn']);
});

test('phone validation', function () {
    $this->actingAs($this->teacher);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('phone', '123') // Too short
        ->call('update')
        ->assertHasErrors(['phone']);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('phone', '081234567890') // Valid format
        ->call('update')
        ->assertHasNoErrors(['phone']);
});

test('success message after update', function () {
    $this->actingAs($this->teacher);

    Livewire::test(EditStudent::class, ['student' => $this->student])
        ->set('name', 'Updated Name')
        ->call('update')
        ->assertRedirect(route('teacher.students.index'));

    // Check session flash message with correct key
    expect(session('success'))->toBe('Student updated successfully.');
});

test('form displays current student data', function () {
    $this->actingAs($this->teacher);

    $response = $this->get(route('students.edit', $this->student));

    $response->assertSee($this->student->name);
    $response->assertSee($this->student->nisn);
    $response->assertSee($this->student->email);
    $response->assertSee($this->student->phone);
    $response->assertSee($this->student->address);
});
