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
        $student = Student::inRandomOrder()->first();
        // Ensure we pick an open batch, or handle cases where no open batches exist
        $scholarshipBatch = ScholarshipBatch::where('status', 'open')->inRandomOrder()->first();

        if (!$student || !$scholarshipBatch) {
            // Fallback or skip creation if necessary dependencies are not met
            // This might happen if you run seeders in an order that doesn't guarantee students/batches exist
            // Or if there are no 'open' batches.
            // For simplicity, we'll return an empty array, but you might want to throw an exception
            // or ensure your StudentSeeder and ScholarshipBatchSeeder run first and create open batches.
            if (!$student) {
                echo "StudentSubmissionFactory: No students found. Skipping submission creation.\n";
                return [];
            }
            if (!$scholarshipBatch) {
                echo "StudentSubmissionFactory: No open scholarship batches found. Skipping submission creation.\n";
                return [];
            }
        }

        $submissionDate = Carbon::instance(fake()->dateTimeBetween($scholarshipBatch->start_date, $scholarshipBatch->end_date));

        $rawCriteriaValues = [];
        if (is_array($scholarshipBatch->criteria_config)) {
            foreach ($scholarshipBatch->criteria_config as $criterion) {
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
        }

        return [
            'student_id' => $student->id,
            'scholarship_batch_id' => $scholarshipBatch->id,
            'submitted_by_teacher_id' => $student->teacher_id, // Assuming student has a teacher_id
            'submission_date' => $submissionDate,
            'raw_criteria_values' => $rawCriteriaValues,
            'normalized_scores' => null, // To be calculated later
            'final_saw_score' => null,   // To be calculated later
            'status' => 'pending', // Default status
        ];
    }
}
