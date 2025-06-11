<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\ScholarshipBatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentSubmission>
 */
class StudentSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate default raw criteria values for testing
        $defaultRawCriteriaValues = [
            'academic_score' => fake()->numberBetween(70, 100),
            'family_income' => fake()->numberBetween(1000000, 5000000),
            'extracurricular_participation' => fake()->randomElement(['High', 'Medium', 'Low']),
        ];

        return [
            'student_id' => Student::factory(),
            'scholarship_batch_id' => ScholarshipBatch::factory(),
            'submitted_by_teacher_id' => User::factory(),
            'submission_date' => Carbon::now()->subDays(rand(1, 30)),
            'raw_criteria_values' => $defaultRawCriteriaValues,
            'normalized_scores' => null, // To be calculated later
            'final_saw_score' => null,   // To be calculated later
            'status' => 'pending', // Default status
        ];
    }

    /**
     * Create a submission with specific scholarship batch and student
     */
    public function forBatch(ScholarshipBatch $batch, ?Student $student = null): static
    {
        $student = $student ?: Student::factory()->create();

        // Generate criteria values based on batch configuration
        $rawCriteriaValues = [];
        if (is_array($batch->criteria_config)) {
            foreach ($batch->criteria_config as $criterion) {
                $value = null;
                if (isset($criterion['data_type']) && $criterion['data_type'] === 'numeric') {
                    $min = $criterion['min_value'] ?? 0;
                    $max = $criterion['max_value'] ?? 100;
                    $value = fake()->numberBetween($min, $max);
                } elseif (isset($criterion['data_type']) && $criterion['data_type'] === 'qualitative') {
                    if (!empty($criterion['value_map']) && is_array($criterion['value_map'])) {
                        $value = fake()->randomElement(array_keys($criterion['value_map']));
                    } else {
                        $value = 'N/A'; // Fallback if value_map is not properly defined
                    }
                } else {
                    // Fallback for criteria without data_type defined
                    if (isset($criterion['min_value']) || isset($criterion['max_value'])) {
                        // Assume numeric if min/max values are defined
                        $min = $criterion['min_value'] ?? 0;
                        $max = $criterion['max_value'] ?? 100;
                        $value = fake()->numberBetween($min, $max);
                    } elseif (!empty($criterion['value_map'])) {
                        // Assume qualitative if value_map is defined
                        $value = fake()->randomElement(array_keys($criterion['value_map']));
                    } else {
                        // Final fallback
                        $value = fake()->numberBetween(0, 100);
                    }
                }
                $rawCriteriaValues[$criterion['name']] = $value;
            }
        } else {
            // Use default criteria if batch doesn't have criteria_config
            $rawCriteriaValues = [
                'academic_score' => fake()->numberBetween(70, 100),
                'family_income' => fake()->numberBetween(1000000, 5000000),
                'extracurricular_participation' => fake()->randomElement(['High', 'Medium', 'Low']),
            ];
        }

        $submissionDate = $batch->start_date && $batch->end_date
            ? Carbon::instance(fake()->dateTimeBetween($batch->start_date, $batch->end_date))
            : Carbon::now()->subDays(rand(1, 30));

        return $this->state([
            'student_id' => $student->id,
            'scholarship_batch_id' => $batch->id,
            'submitted_by_teacher_id' => $student->teacher_id ?: User::factory()->create()->id,
            'submission_date' => $submissionDate,
            'raw_criteria_values' => $rawCriteriaValues,
        ]);
    }
}
