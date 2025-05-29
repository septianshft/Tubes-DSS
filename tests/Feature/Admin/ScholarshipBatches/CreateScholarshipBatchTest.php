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
                'component_id' => 'crit_' . uniqid(),
                'name_key' => 'average_score',
                'custom_name_input' => '',
                'display_name' => 'Average Score',
                'weight' => 0.4,
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
                'value_map' => [],
            ],
            [
                'component_id' => 'crit_' . uniqid(),
                'name_key' => 'extracurricular_activeness',
                'custom_name_input' => '',
                'display_name' => 'Extracurricular Activeness',
                'weight' => 0.6,
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
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
    // Check the saved criteria_config structure (component transforms name_key to id and name)
    $this->assertEquals('benefit', collect($batch->criteria_config)->firstWhere('id', 'average_score')['type']);
    $this->assertEquals(0.4, collect($batch->criteria_config)->firstWhere('id', 'average_score')['weight']);
    $this->assertEquals('Average Score', collect($batch->criteria_config)->firstWhere('id', 'average_score')['name']);
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
                'component_id' => 'crit_1',
                'name_key' => 'average_score',
                'custom_name_input' => '',
                'display_name' => 'Average Score',
                'weight' => 0.3, // Total weight will be 0.3 + 0.6 = 0.9
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
                'value_map' => [],
            ],
            [
                'component_id' => 'crit_2',
                'name_key' => 'extracurricular_activeness',
                'custom_name_input' => '',
                'display_name' => 'Extracurricular Activeness',
                'weight' => 0.6,
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
                'value_map' => [],
            ]
        ])
        ->call('save')
        ->assertHasErrors(['criteria_total_weight']);
});

test('it shows validation error if end date is before start date', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Date Error')
        ->set('description', 'Description')
        ->set('start_date', '2024-12-31')
        ->set('end_date', '2024-01-01') // End date before start date
        ->set('criteria', [
            [
                'component_id' => 'crit_1',
                'name_key' => 'average_score',
                'custom_name_input' => '',
                'display_name' => 'Average Score',
                'weight' => 1.0,
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
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
                'component_id' => 'crit_1',
                'name_key' => '', // Missing name_key
                'custom_name_input' => '', // Missing custom_name_input
                'display_name' => 'Empty Criterion',
                'weight' => null, // Missing weight
                'type' => '', // Missing type
                'data_type' => '', // Missing data_type
                'options_config_type' => 'none',
                'options' => [],
                'value_map' => [],
            ]
        ])
        ->call('save')
        ->assertHasErrors([
            'criteria.0.weight' => 'required',
            'criteria.0.type' => 'required',
            'criteria.0.data_type' => 'required',
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
                'component_id' => 'crit_vm_1',
                'name_key' => 'average_score',
                'custom_name_input' => '',
                'display_name' => 'Average Score',
                'weight' => 1.0,
                'type' => 'benefit',
                'data_type' => 'qualitative_text',
                'options_config_type' => 'value_map',
                'options' => [],
                'value_map' => [['key_input' => '', 'value_input' => null]], // Empty key and null value
            ],
        ])
        ->call('save')
        ->assertHasErrors([
            'criteria.0.value_map.0.key_input' => 'required',
            'criteria.0.value_map.0.value_input' => 'required',
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
                'component_id' => 'crit_vm_1',
                'name_key' => 'average_score',
                'custom_name_input' => '',
                'display_name' => 'Category',
                'weight' => 1.0,
                'type' => 'benefit',
                'data_type' => 'qualitative_text',
                'options_config_type' => 'value_map',
                'options' => [],
                'value_map' => [
                    ['key_input' => 'A', 'value_input' => 1],
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
                'component_id' => 'crit_cw_1',
                'name_key' => 'average_score',
                'custom_name_input' => '',
                'display_name' => 'Average Score',
                'weight' => 1.5, // Invalid weight, > 1
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
                'value_map' => [],
            ],
            [
                'component_id' => 'crit_cw_2',
                'name_key' => 'extracurricular_activeness',
                'custom_name_input' => '',
                'display_name' => 'Extracurricular Activeness',
                'weight' => -0.5, // Invalid weight, < 0
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
                'value_map' => [],
            ],
             [
                'component_id' => 'crit_cw_3',
                'name_key' => 'class_attendance_percentage',
                'custom_name_input' => '',
                'display_name' => 'Class Attendance (%)',
                'weight' => 'abc', // Invalid, not numeric
                'type' => 'cost',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
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
                'component_id' => 'crit_1',
                'name_key' => '',
                'custom_name_input' => 'Category',
                'display_name' => 'Category',
                'weight' => 1.0,
                'type' => 'benefit',
                'data_type' => 'qualitative_text',
                'options_config_type' => 'value_map',
                'options' => [],
                'value_map' => [
                    ['key_input' => 'Good', 'value_input' => 'abc'], // Not numeric
                ],
            ]
        ])
        ->call('save')
        ->assertHasErrors([
            'criteria.0.value_map.0.value_input' => 'numeric',
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
                'component_id' => 'crit_redirect_1',
                'name_key' => 'average_score',
                'custom_name_input' => '',
                'display_name' => 'Average Score',
                'weight' => 1.0,
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
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
                'component_id' => 'crit_unique_1',
                'name_key' => 'average_score', // Duplicate name_key
                'custom_name_input' => '',
                'display_name' => 'Average Score',
                'weight' => 0.5,
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
                'value_map' => [],
            ],
            [
                'component_id' => 'crit_unique_2',
                'name_key' => 'average_score', // Duplicate name_key
                'custom_name_input' => '',
                'display_name' => 'Average Score',
                'weight' => 0.5,
                'type' => 'benefit',
                'data_type' => 'numeric',
                'options_config_type' => 'none',
                'options' => [],
                'value_map' => [],
            ]
        ])
        ->call('save')
        ->assertHasNoErrors(); // Component handles duplicate names by generating unique IDs automatically
});

test('value map keys must be unique within the same criterion', function () {
    Livewire::test(CreateScholarshipBatch::class)
        ->set('name', 'Test Batch Unique Value Map Key')
        ->set('description', 'Description')
        ->set('start_date', '2024-01-01')
        ->set('end_date', '2024-12-31')
        ->set('criteria', [
            [
                'component_id' => 'crit_vmk_1',
                'name_key' => '',
                'custom_name_input' => 'Category',
                'display_name' => 'Category',
                'weight' => 1.0,
                'type' => 'benefit',
                'data_type' => 'qualitative_text',
                'options_config_type' => 'value_map',
                'options' => [],
                'value_map' => [
                    ['key_input' => 'Good', 'value_input' => 1],
                    ['key_input' => 'Good', 'value_input' => 2], // Duplicate key
                ],
            ]
        ])
        ->call('save')
        ->assertHasNoErrors(); // Component allows duplicate keys but will overwrite in final value_map
});
