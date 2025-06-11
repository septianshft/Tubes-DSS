<?php

use App\Models\ScholarshipBatch;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentSubmission;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Carbon\Carbon;
use App\Livewire\Admin\ScholarshipBatches\ListScholarshipBatches;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    // Create admin user
    $this->admin = User::role('admin')->first();

    // Create test scholarship batches
    $this->activeBatch = ScholarshipBatch::factory()->create([
        'name' => 'Active Scholarship Batch',
        'status' => 'active',
        'start_date' => Carbon::now()->subDays(5),
        'end_date' => Carbon::now()->addDays(25),
    ]);

    $this->closedBatch = ScholarshipBatch::factory()->create([
        'name' => 'Closed Scholarship Batch',
        'status' => 'closed',
        'start_date' => Carbon::now()->subDays(30),
        'end_date' => Carbon::now()->subDays(5),
    ]);    $this->upcomingBatch = ScholarshipBatch::factory()->create([
        'name' => 'Upcoming Scholarship Batch',
        'status' => 'draft',
        'start_date' => Carbon::now()->addDays(5),
        'end_date' => Carbon::now()->addDays(35),
    ]);

    // Create some test submissions
    $student = Student::factory()->create();
    $teacher = User::role('teacher')->first();

    StudentSubmission::factory()->create([
        'scholarship_batch_id' => $this->activeBatch->id,
        'student_id' => $student->id,
        'submitted_by_teacher_id' => $teacher->id,
        'status' => 'pending',
        'raw_criteria_values' => [
            'academic_score' => 85,
            'family_income' => 2500000,
            'extracurricular_participation' => 'High',
        ],
    ]);
});

test('admin can access scholarship batches list', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.scholarship-batches.index'))
        ->assertOk()
        ->assertSeeLivewire('admin.scholarship-batches.list-scholarship-batches');
});

test('non admin cannot access scholarship batches list', function () {
    $teacher = User::role('teacher')->first();

    $this->actingAs($teacher)
        ->get(route('admin.scholarship-batches.index'))
        ->assertStatus(403);
});

test('guest cannot access scholarship batches list', function () {
    $this->get(route('admin.scholarship-batches.index'))
        ->assertRedirect('/login');
});

test('component displays statistics correctly', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->assertSee('3') // Total batches
        ->assertSee('1') // Active batches
        ->assertSee('1') // Upcoming batches
        ->assertSee('1') // Closed batches
        ->assertSee('1'); // Total submissions
});

test('search functionality works', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('search', 'Active')
        ->assertSee('Active Scholarship Batch')
        ->assertDontSee('Closed Scholarship Batch')
        ->assertDontSee('Upcoming Scholarship Batch');
});

test('status filter works', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('statusFilter', 'active')
        ->assertSee('Active Scholarship Batch')
        ->assertDontSee('Closed Scholarship Batch')
        ->assertDontSee('Upcoming Scholarship Batch');
});

test('sorting functionality works', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->call('sortBy', 'name')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortDirection', 'asc');
});

test('bulk selection works', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('selectedBatches', [$this->activeBatch->id, $this->upcomingBatch->id])
        ->assertSet('selectedBatches', [$this->activeBatch->id, $this->upcomingBatch->id]);
});

test('batch activation workflow', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->call('confirmActivateBatch', $this->upcomingBatch->id)
        ->assertSet('confirmingActivateBatch', true)
        ->assertSet('batchIdToActivate', $this->upcomingBatch->id)
        ->call('activateBatch')
        ->assertSet('confirmingActivateBatch', false)
        ->assertHasNoErrors();

    $this->upcomingBatch->refresh();
    expect($this->upcomingBatch->status)->toBe('active');
});

test('batch close workflow', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->call('confirmCloseBatch', $this->activeBatch->id)
        ->assertSet('confirmingCloseBatch', true)
        ->assertSet('batchIdToClose', $this->activeBatch->id)
        ->call('closeBatch')
        ->assertSet('confirmingCloseBatch', false)
        ->assertHasNoErrors();

    $this->activeBatch->refresh();
    expect($this->activeBatch->status)->toBe('closed');
});

test('bulk activate batches', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('selectedBatches', [$this->upcomingBatch->id])
        ->call('bulkActivate')
        ->assertHasNoErrors();

    $this->upcomingBatch->refresh();
    expect($this->upcomingBatch->status)->toBe('active');
});

test('bulk close batches', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('selectedBatches', [$this->activeBatch->id])
        ->call('bulkClose')
        ->assertHasNoErrors();

    $this->activeBatch->refresh();
    expect($this->activeBatch->status)->toBe('closed');
});

test('bulk delete workflow', function () {
    $batchToDelete = ScholarshipBatch::factory()->create(['name' => 'Test Delete Batch']);
    $teacher = User::role('teacher')->first();

    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('selectedBatches', [$batchToDelete->id])
        ->call('confirmBulkDelete')
        ->assertSet('confirmingBulkDelete', true)
        ->assertSet('batchesToDelete', [$batchToDelete->id])
        ->call('bulkDelete')
        ->assertSet('confirmingBulkDelete', false)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('scholarship_batches', ['id' => $batchToDelete->id]);
});

test('select all functionality', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('selectAll', true)
        ->assertSet('selectAll', true);
});

test('clear selection functionality', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('selectedBatches', [$this->activeBatch->id])
        ->call('clearSelection')
        ->assertSet('selectedBatches', [])
        ->assertSet('selectAll', false);
});

test('pagination controls', function () {
    // Create more batches to test pagination
    ScholarshipBatch::factory()->count(15)->create();

    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->set('perPage', 5)
        ->assertSee('Next'); // Should have pagination
});

test('modal reset functions', function () {
    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->call('confirmActivateBatch', $this->upcomingBatch->id)
        ->call('resetActivateConfirmation')
        ->assertSet('confirmingActivateBatch', false)
        ->assertSet('batchIdToActivate', null);
});

test('statistics calculation', function () {
    $component = Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class);

    $statistics = $component->get('statistics');

    expect($statistics['total_batches'])->toBe(3);
    expect($statistics['active_batches'])->toBe(1);
    expect($statistics['upcoming_batches'])->toBe(1);
    expect($statistics['closed_batches'])->toBe(1);
    expect($statistics['total_submissions'])->toBe(1);
});

test('batch deletion individual', function () {
    $batchToDelete = ScholarshipBatch::factory()->create(['name' => 'Individual Delete Test']);
    $teacher = User::role('teacher')->first();

    Livewire::actingAs($this->admin)
        ->test(ListScholarshipBatches::class)
        ->call('deleteBatch', $batchToDelete->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('scholarship_batches', ['id' => $batchToDelete->id]);
});

test('computed status display', function () {
    // Test for computed status display in the view
    $this->actingAs($this->admin)
        ->get(route('admin.scholarship-batches.index'))
        ->assertSee('Active')
        ->assertSee('Closed')
        ->assertSee('Upcoming');
});

test('empty state display', function () {
    // Delete all batches to test empty state
    ScholarshipBatch::query()->delete();

    $this->actingAs($this->admin)
        ->get(route('admin.scholarship-batches.index'))
        ->assertSee('No scholarship batches found')
        ->assertSee('Why not create one now?');
});
