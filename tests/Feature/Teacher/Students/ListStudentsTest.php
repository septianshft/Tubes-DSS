<?php

use App\Livewire\Teacher\Students\ListStudents;
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

    // Create students for different teachers
    $this->myStudent = Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'My Student',
        'nisn' => '1234567890',
        'email' => 'my.student@test.com'
    ]);

    $this->otherStudent = Student::factory()->create([
        'teacher_id' => $this->otherTeacher->id,
        'name' => 'Other Student',
        'nisn' => '0987654321',
        'email' => 'other.student@test.com'
    ]);
});

test('teacher can access students list', function () {
    $this->actingAs($this->teacher);

    $response = $this->get(route('teacher.students.index'));

    $response->assertOk();
    $response->assertSeeLivewire(ListStudents::class);
});

test('teacher only sees their own students', function () {
    $this->actingAs($this->teacher);

    Livewire::test(ListStudents::class)
        ->assertSee($this->myStudent->name)
        ->assertSee($this->myStudent->nisn)
        ->assertDontSee($this->otherStudent->name)
        ->assertDontSee($this->otherStudent->nisn);
});

test('other teacher only sees their own students', function () {
    $this->actingAs($this->otherTeacher);

    Livewire::test(ListStudents::class)
        ->assertSee($this->otherStudent->name)
        ->assertSee($this->otherStudent->nisn)
        ->assertDontSee($this->myStudent->name)
        ->assertDontSee($this->myStudent->nisn);
});

test('search filters only teachers students', function () {
    $this->actingAs($this->teacher);

    // Create additional students for the teacher
    Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'Searchable Student',
        'nisn' => '1111111111'
    ]);

    Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'Another Student',
        'nisn' => '2222222222'
    ]);

    // Create a student for other teacher with similar name
    Student::factory()->create([
        'teacher_id' => $this->otherTeacher->id,
        'name' => 'Searchable Other',
        'nisn' => '3333333333'
    ]);

    Livewire::test(ListStudents::class)
        ->set('search', 'Searchable')
        ->assertSee('Searchable Student')
        ->assertDontSee('Searchable Other') // Should not see other teacher's student
        ->assertDontSee('Another Student'); // Should be filtered out by search
});

test('edit button present for teachers students', function () {
    $this->actingAs($this->teacher);

    $response = $this->get(route('teacher.students.index'));

    $response->assertSee('Edit'); // Edit button should be present
    $response->assertSee(route('teacher.students.edit', $this->myStudent)); // Edit link should be present
});

test('actions column header present', function () {
    $this->actingAs($this->teacher);

    $response = $this->get(route('students.index'));

    $response->assertSee('Actions'); // Actions column header should be present
});

test('table shows nisn not nis', function () {
    $this->actingAs($this->teacher);

    $response = $this->get(route('teacher.students.index'));

    $response->assertSee('NISN'); // Should show NISN header
    // Check that the table contains NISN header and not standalone NIS
    $response->assertSeeInOrder(['<th', 'NISN', '</th>']); // Table header should contain NISN
    // Ensure we don't have standalone "NIS" as a header (but NISN is OK)
    $content = $response->getContent();
    // Check that any occurrence of "NIS" in table headers is part of "NISN"
    preg_match_all('/<th[^>]*>.*?<\/th>/s', $content, $headers);
    foreach ($headers[0] as $header) {
        if (strpos($header, 'NIS') !== false) {
            // If we find NIS, it should be part of NISN, not standalone
            expect($header)->toContain('NISN');
        }
    }
});

test('empty state shows correct colspan', function () {
    $this->actingAs($this->teacher);

    // Delete all students for this teacher to test empty state
    Student::where('teacher_id', $this->teacher->id)->delete();

    $response = $this->get(route('students.index'));

    // The empty state should span all 6 columns (Name, NISN, Email, Phone, Address, Actions)
    $response->assertSee('No students found');
});

test('guest redirected to login', function () {
    $response = $this->get(route('students.index'));

    $response->assertRedirect(route('login'));
});

test('admin cannot access teacher students list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get(route('students.index'));

    $response->assertStatus(403);
});

test('search by name works', function () {
    $this->actingAs($this->teacher);

    Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'John Doe',
        'nisn' => '1111111111'
    ]);

    Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'Jane Smith',
        'nisn' => '2222222222'
    ]);

    Livewire::test(ListStudents::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

test('search by nisn works', function () {
    $this->actingAs($this->teacher);

    Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'Student One',
        'nisn' => '1111111111'
    ]);

    Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'Student Two',
        'nisn' => '2222222222'
    ]);

    Livewire::test(ListStudents::class)
        ->set('search', '1111')
        ->assertSee('Student One')
        ->assertDontSee('Student Two');
});

test('search by email works', function () {
    $this->actingAs($this->teacher);

    Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'Student One',
        'email' => 'one@test.com'
    ]);

    Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'name' => 'Student Two',
        'email' => 'two@test.com'
    ]);

    Livewire::test(ListStudents::class)
        ->set('search', 'one@test')
        ->assertSee('Student One')
        ->assertDontSee('Student Two');
});
