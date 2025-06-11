<?php

namespace Tests\Feature\Teacher\Submissions;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\ScholarshipBatch;
use App\Services\PredefinedCriteriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Teacher\Submissions\CreateStudentSubmissionForBatch;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class PredefinedCriteriaDescriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected ScholarshipBatch $batchWithPredefinedCriteria;
    protected Student $student;
    protected PredefinedCriteriaService $predefinedCriteriaService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'admin']);

        // Create teacher user
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        // Initialize predefined criteria service
        $this->predefinedCriteriaService = new PredefinedCriteriaService();

        // Create a scholarship batch with predefined criteria (including descriptions)
        $this->batchWithPredefinedCriteria = ScholarshipBatch::factory()->create([
            'status' => 'open',
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::tomorrow(),
            'criteria_config' => [
                [
                    'id' => 'average_score',
                    'name' => 'Nilai Rata-Rata Siswa',
                    'weight' => 0.5,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                    'predefined' => true,
                    'description' => 'Rata-rata nilai akademik siswa selama semester terakhir. Semakin tinggi nilai, semakin baik peluang mendapat beasiswa.',
                ],
                [
                    'id' => 'family_income',
                    'name' => 'Penghasilan Keluarga per Bulan',
                    'weight' => 0.5,
                    'type' => 'cost',
                    'data_type' => 'numeric',
                    'predefined' => true,
                    'description' => 'Total penghasilan keluarga per bulan dalam rupiah. Semakin rendah penghasilan, semakin prioritas untuk mendapat beasiswa.',
                ]
            ]
        ]);

        // Create a student assigned to this teacher
        $this->student = Student::factory()->create(['teacher_id' => $this->teacher->id]);
    }

    public function test_teacher_can_see_predefined_criteria_descriptions()
    {
        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $this->batchWithPredefinedCriteria])
            ->assertSee('Rata-rata nilai akademik siswa selama semester terakhir')
            ->assertSee('Total penghasilan keluarga per bulan dalam rupiah');
    }

    public function test_teacher_interface_displays_descriptions_in_correct_format()
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.submissions.create-for-batch', $this->batchWithPredefinedCriteria));

        $response->assertOk()
            ->assertSee('Rata-rata nilai akademik siswa selama semester terakhir')
            ->assertSee('Total penghasilan keluarga per bulan dalam rupiah')
            ->assertSee('text-xs text-gray-500'); // Check that descriptions use the correct CSS classes
    }

    public function test_predefined_criteria_config_includes_descriptions()
    {
        // Test that the PredefinedCriteriaService properly includes descriptions
        $averageScoreConfig = $this->predefinedCriteriaService->getPredefinedCriteriaConfig('average_score', 0.5);

        $this->assertNotNull($averageScoreConfig);
        $this->assertArrayHasKey('description', $averageScoreConfig);
        $this->assertStringContainsString('Rata-rata nilai akademik', $averageScoreConfig['description']);

        $familyIncomeConfig = $this->predefinedCriteriaService->getPredefinedCriteriaConfig('family_income', 0.5);

        $this->assertNotNull($familyIncomeConfig);
        $this->assertArrayHasKey('description', $familyIncomeConfig);
        $this->assertStringContainsString('penghasilan keluarga', $familyIncomeConfig['description']);
    }

    public function test_batch_without_descriptions_still_works()
    {
        // Create a batch with criteria that don't have descriptions
        $batchWithoutDescriptions = ScholarshipBatch::factory()->create([
            'status' => 'open',
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::tomorrow(),
            'criteria_config' => [
                [
                    'id' => 'custom_criterion',
                    'name' => 'Custom Criterion Without Description',
                    'weight' => 1.0,
                    'type' => 'benefit',
                    'data_type' => 'numeric',
                    // Note: no 'description' field
                ]
            ]
        ]);

        Livewire::actingAs($this->teacher)
            ->test(CreateStudentSubmissionForBatch::class, ['batch' => $batchWithoutDescriptions])
            ->assertSee('Custom Criterion Without Description')
            ->assertDontSee('text-xs text-gray-500'); // Should not show description styling when there's no description
    }
}
