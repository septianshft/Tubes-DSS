<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScholarshipBatch>
 */
class ScholarshipBatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::instance(fake()->dateTimeBetween('-1 month', '+1 month'));
        $endDate = $startDate->copy()->addDays(fake()->numberBetween(14, 60)); // Duration between 2 weeks to 2 months

        $status = 'draft';
        if ($startDate->isPast() && $endDate->isFuture()) {
            $status = 'open';
        } elseif ($startDate->isFuture()) {
            $status = 'upcoming'; // Custom status, ensure your app handles this or stick to defined ones
        } elseif ($endDate->isPast()) {
            $status = 'closed';
        }

        // More detailed and varied criteria_config
        $criteriaSets = [
            [
                ['id' => 'academic_performance', 'name' => 'Academic Performance', 'weight' => 0.4, 'type' => 'benefit', 'data_type' => 'numeric', 'min_value' => 0, 'max_value' => 100, 'rules' => 'required|numeric|min:0|max:100', 'student_model_key' => 'average_score'],
                ['id' => 'class_attendance', 'name' => 'Class Attendance', 'weight' => 0.2, 'type' => 'benefit', 'data_type' => 'numeric', 'min_value' => 0, 'max_value' => 100, 'rules' => 'required|numeric|min:0|max:100', 'student_model_key' => 'class_attendance_percentage'],
                ['id' => 'extracurricular_involvement', 'name' => 'Extracurricular Involvement', 'weight' => 0.25, 'type' => 'benefit', 'data_type' => 'qualitative', 'value_map' => ['None' => 1, 'Member' => 2, 'Secretary' => 3, 'Treasurer' => 4, 'Chairman' => 5], 'rules' => 'required|string', 'student_model_key' => 'extracurricular_position'], // Example, adjust student_model_key if needed or handle differently
                ['id' => 'tuition_payment_delays', 'name' => 'Tuition Payment Delays', 'weight' => 0.15, 'type' => 'cost', 'data_type' => 'numeric', 'min_value' => 0, 'max_value' => 10, 'rules' => 'required|integer|min:0', 'student_model_key' => 'tuition_payment_delays'],
            ],
            [
                ['id' => 'avg_report_score', 'name' => 'Average Report Score', 'weight' => 0.5, 'type' => 'benefit', 'data_type' => 'numeric', 'min_value' => 0, 'max_value' => 100, 'rules' => 'required|numeric|min:0|max:100'],
                ['id' => 'leadership_skills', 'name' => 'Leadership Skills', 'weight' => 0.3, 'type' => 'benefit', 'data_type' => 'qualitative', 'value_map' => ['Low' => 1, 'Medium' => 3, 'High' => 5], 'rules' => 'required|string'],
                ['id' => 'financial_need', 'name' => 'Financial Need', 'weight' => 0.2, 'type' => 'benefit', 'data_type' => 'qualitative', 'value_map' => ['Low' => 1, 'Medium' => 3, 'High' => 5], 'rules' => 'required|string'],
            ],
            [
                ['id' => 'gpa', 'name' => 'GPA', 'weight' => 0.6, 'type' => 'benefit', 'data_type' => 'numeric', 'min_value' => 0, 'max_value' => 4.00, 'rules' => 'required|numeric|min:0|max:4'],
                ['id' => 'disciplinary_record', 'name' => 'Disciplinary Record', 'weight' => 0.2, 'type' => 'cost', 'data_type' => 'qualitative', 'value_map' => ['Good' => 0, 'Minor Infraction' => 1, 'Major Infraction' => 3], 'rules' => 'required|string'],
                ['id' => 'community_service_hours', 'name' => 'Community Service Hours', 'weight' => 0.2, 'type' => 'benefit', 'data_type' => 'numeric', 'min_value' => 0, 'max_value' => 200, 'rules' => 'required|integer|min:0'],
            ]
        ];


        return [
            'name' => 'Beasiswa ' . fake()->words(2, true) . ' ' . $startDate->year,
            'description' => fake()->paragraph(3),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'status' => $status,
            'criteria_config' => fake()->randomElement($criteriaSets),
        ];
    }
}
