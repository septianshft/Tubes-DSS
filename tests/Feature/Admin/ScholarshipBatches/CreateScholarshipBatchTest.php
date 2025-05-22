<?php

namespace Tests\Feature\Admin\ScholarshipBatches;

use App\Livewire\Admin\ScholarshipBatches\CreateScholarshipBatch;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::role('admin')->first();
    $this->actingAs($this->admin);
});

test('admin can access the create scholarship batch page', function () {
    $this->get(route('admin.scholarship-batches.create'))
        ->assertStatus(200)
        ->assertSeeLivewire(CreateScholarshipBatch::class);
});

test('it can create a scholarship batch with valid data', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch 2024')
        ->set('description', 'This is a test batch for 2024.')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-gpa-' . uniqid(),
                'name' => 'average_score', // Changed from GPA to average_score to match available criteria
                'display_name' => 'Average Score',
                'weight' => 0.4,
                'type' => 'benefit', // Changed from numeric to benefit
                'value_map_enabled' => false,
                'value_map' => [],
            ],
            [
                'id' => 'criterion-income-' . uniqid(),
                'name' => 'extracurricular_activeness', // Changed from Income to extracurricular_activeness
                'display_name' => 'Extracurricular Activeness',
                'weight' => 0.6,
                'type' => 'benefit', // Changed from numeric to benefit
                'value_map_enabled' => false,
                'value_map' => [],
            ],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.scholarship-batches.index'));

    $this->assertDatabaseHas('scholarship_batches', [
        'name' => 'Test Batch 2024',
        'description' => 'This is a test batch for 2024.',
    ]);

    $batch = \App\Models\ScholarshipBatch::where('name', 'Test Batch 2024')->first();
    $this->assertCount(2, $batch->criteria_config);
    // Adjust assertions to match the new criteria_config structure if needed
    $this->assertEquals('benefit', collect($batch->criteria_config)->firstWhere('name', 'average_score')['type']);
    $this->assertEquals(0.4, collect($batch->criteria_config)->firstWhere('name', 'average_score')['weight']);
});

test('it shows validation errors for missing required fields', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->call('save')
        ->assertHasErrors([
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
});

test('it shows validation error if criteria total weight is not 1.0', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Weight Error')
        ->set('description', 'Description')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-1',
                'name' => 'average_score',
                'display_name' => 'Average Score',
                'weight' => 0.3, // Total weight will be 0.3 + 0.6 = 0.9
                'type' => 'benefit',
                'value_map_enabled' => false,
                'value_map' => [],
            ],
            [
                'id' => 'criterion-2',
                'name' => 'extracurricular_activeness',
                'display_name' => 'Extracurricular Activeness',
                'weight' => 0.6,
                'type' => 'benefit',
                'value_map_enabled' => false,
                'value_map' => [],
            ]
        ])
        ->call('save')
        ->assertHasErrors(['criteria_total_weight' => 'The sum of all criteria weights must be exactly 1.']);
});

test('it shows validation error if end date is before start date', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Date Error')
        ->set('description', 'Description')
        ->set('start_date', '2024-12-31')
        ->set('end_date', '2024-01-01') // End date before start date
        ->set('criteria', [
            [
                'id' => 'criterion-1',
                'name' => 'GPA',
                'weight' => 1.0,
                'type' => 'numeric',
                'value_map_enabled' => false,
                'value_map' => [],
            ]
        ])
        ->call('save')
        ->assertHasErrors(['end_date' => 'after_or_equal']);
});

test('it shows validation errors for individual criterion fields', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Criterion Error')
        ->set('description', 'Description')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-1',
                'name' => '', // Missing name
                'weight' => null, // Missing weight
                'type' => '', // Missing type
                'value_map_enabled' => false,
                'value_map' => [],
            ]
        ])
        ->call('save')
        ->assertHasErrors([
            'criteria.0.name' => 'required',
            'criteria.0.weight' => 'required',
            'criteria.0.type' => 'required',
        ]);
});

test('it shows validation errors for value map entries if enabled and empty', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Value Map Error')
        ->set('description', 'Description')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-vm-1',
                'name' => 'average_score',
                'display_name' => 'Average Score',
                'weight' => 1.0,
                'type' => 'benefit',
                'value_map_enabled' => true,
                'value_map' => [['key' => '', 'value' => null]], // Empty key and null value
            ],
        ])
        ->call('save')
        ->assertHasErrors([
            'criteria.0.value_map.0.key' => 'required_if',
            'criteria.0.value_map.0.value' => 'required_if',
        ]);
});


test('admin can add and remove criteria dynamically', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->call('addCriterion')
        ->assertSet('criteria', function ($criteria) {
            return count($criteria) === 2; // Starts with 1, adds 1
        })
        ->call('addCriterion')
        ->assertSet('criteria', function ($criteria) {
            return count($criteria) === 3;
        })
        ->call('removeCriterion', 1) // Remove the second criterion (index 1)
        ->assertSet('criteria', function ($criteria) {
            return count($criteria) === 2;
        })
        ->call('removeCriterion', 0) // Remove the first criterion (index 0)
        ->assertSet('criteria', function ($criteria) {
            return count($criteria) === 1; // Should not allow removing the last one, or it should re-add one.
                                            // Current component logic ensures at least one criterion.
        });
});


test('admin can add and remove value map entries dynamically', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('criteria', [
            [
                'id' => 'criterion-vm-1',
                'name' => 'Category',
                'weight' => 1.0,
                'type' => 'text',
                'value_map_enabled' => true,
                'value_map' => [
                    ['key' => 'A', 'value' => 1],
                ],
            ]
        ])
        ->call('addValueMapEntry', 0) // Add to first criterion
        ->assertSet('criteria.0.value_map', function ($value_map) {
            return count($value_map) === 2;
        })
        ->call('addValueMapEntry', 0)
        ->assertSet('criteria.0.value_map', function ($value_map) {
            return count($value_map) === 3;
        })
        ->call('removeValueMapEntry', 0, 1) // Remove from first criterion, second entry
        ->assertSet('criteria.0.value_map', function ($value_map) {
            return count($value_map) === 2;
        })
        ->call('removeValueMapEntry', 0, 0) // Remove the first entry
         ->assertSet('criteria.0.value_map', function ($value_map) {
            return count($value_map) === 1; // Current component logic ensures at least one value map entry if enabled.
        });
});


test('non-admin user cannot access the create scholarship batch page', function () {
    $teacher = User::role('teacher')->first();
    $this->actingAs($teacher);

    $this->get(route('admin.scholarship-batches.create'))
        ->assertStatus(403); // Forbidden
});

test('guest user cannot access the create scholarship batch page', function () {
    $this->post(route('logout')); // Ensure no user is authenticated

    $this->get(route('admin.scholarship-batches.create'))
        ->assertRedirect(route('login'));
});

test('criteria weight must be numeric and between 0 and 1', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Criteria Weight Validation')
        ->set('description', 'Description')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-cw-1',
                'name' => 'average_score',
                'display_name' => 'Average Score',
                'weight' => 1.5, // Invalid weight, > 1
                'type' => 'benefit',
                'value_map_enabled' => false,
                'value_map' => [],
            ],
            [
                'id' => 'criterion-cw-2',
                'name' => 'extracurricular_activeness',
                'display_name' => 'Extracurricular Activeness',
                'weight' => -0.5, // Invalid weight, < 0
                'type' => 'benefit',
                'value_map_enabled' => false,
                'value_map' => [],
            ],
             [
                'id' => 'criterion-cw-3',
                'name' => 'class_attendance_percentage',
                'display_name' => 'Class Attendance (%)',
                'weight' => 'abc', // Invalid, not numeric
                'type' => 'cost',
                'value_map_enabled' => false,
                'value_map' => [],
            ],
        ])
        ->call('save') // Call save to trigger validation
        ->assertHasErrors([
            'criteria.0.weight' => 'max',
            'criteria.1.weight' => 'min',
            'criteria.2.weight' => 'numeric',
        ]);
});

test('value map value must be numeric if type is text with mapping', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Value Map Numeric')
        ->set('description', 'Description')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-1',
                'name' => 'Category',
                'weight' => 1.0,
                'type' => 'text',
                'value_map_enabled' => true,
                'value_map' => [
                    ['key' => 'Good', 'value' => 'abc'], // Not numeric
                ],
            ]
        ])
        ->call('save')
        ->assertHasErrors([
            'criteria.0.value_map.0.value' => 'numeric',
        ]);
});

test('it redirects to index page with success message after saving', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Redirect')
        ->set('description', 'Description for redirect test')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-redirect-1',
                'name' => 'average_score',
                'display_name' => 'Average Score',
                'weight' => 1.0,
                'type' => 'benefit',
                'value_map_enabled' => false,
                'value_map' => [],
            ]
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.scholarship-batches.index'));

    // Optionally, check for the flash message if your setup supports it easily
    // $this->assertTrue(session()->has('message'));
    // $this->assertEquals('Scholarship batch created successfully.', session('message'));
});

test('criteria name must be unique within the same batch', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Unique Criteria Name')
        ->set('description', 'Description')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-unique-1',
                'name' => 'average_score', // Duplicate name
                'display_name' => 'Average Score',
                'weight' => 0.5,
                'type' => 'benefit',
                'value_map_enabled' => false,
                'value_map' => [],
            ],
            [
                'id' => 'criterion-unique-2',
                'name' => 'average_score', // Duplicate name
                'display_name' => 'Average Score',
                'weight' => 0.5,
                'type' => 'benefit',
                'value_map_enabled' => false,
                'value_map' => [],
            ]
        ])
        ->call('save')
        ->assertHasErrors(['criteria.0.name' => 'distinct', 'criteria.1.name' => 'distinct']);
});

test('value map keys must be unique within the same criterion', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Unique Value Map Key')
        ->set('description', 'Description')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'id' => 'criterion-vmk-1',
                'name' => 'Category',
                'weight' => 1.0,
                'type' => 'text',
                'value_map_enabled' => true,
                'value_map' => [
                    ['key' => 'Good', 'value' => 1],
                    ['key' => 'Good', 'value' => 2], // Duplicate key
                ],
            ]
        ])
        ->call('save')
        ->assertHasErrors(['criteria.0.value_map.1.key' => 'distinct']);
});
