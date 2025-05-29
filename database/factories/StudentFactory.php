<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => User::role('teacher')->inRandomOrder()->first()->id ?? User::factory()->create()->assignRole('teacher')->id,
            'name' => fake()->name(),
            'nisn' => fake()->unique()->numerify('##########'), // 10 digit NISN
            'date_of_birth' => fake()->dateTimeBetween('-18 years', '-15 years')->format('Y-m-d'),
            'address' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'extracurricular_position' => fake()->randomElement(['Chairman', 'Secretary', 'Treasurer', 'Member', 'None']),
            'extracurricular_activeness' => fake()->numberBetween(1, 5),
            'class_attendance_percentage' => fake()->numberBetween(70, 100),
            'average_score' => fake()->randomFloat(2, 70, 99),
            'tuition_payment_delays' => fake()->numberBetween(0, 3),
        ];
    }
}
