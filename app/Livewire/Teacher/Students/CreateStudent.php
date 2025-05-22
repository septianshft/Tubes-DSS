<?php

namespace App\Livewire\Teacher\Students;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CreateStudent extends Component
{
    public string $name = '';
    public string $nisn = '';
    public string $date_of_birth = '';
    public string $address = '';
    public ?string $extracurricular_position = null;
    public ?int $extracurricular_activeness = null; // Changed from ?string to ?int
    public ?float $class_attendance_percentage = null;
    public ?float $average_score = null;
    public ?int $tuition_payment_delays = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|unique:students,nisn|max:255',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'extracurricular_position' => 'nullable|string|max:255',
            'extracurricular_activeness' => 'nullable|integer|min:0|max:100', // Changed validation to integer
            'class_attendance_percentage' => 'nullable|numeric|min:0|max:100',
            'average_score' => 'nullable|numeric|min:0|max:100',
            'tuition_payment_delays' => 'nullable|integer|min:0',
        ];
    }

    public function save(): void
    {
        $this->validate();

        Student::create([
            'teacher_id' => Auth::id(),
            'name' => $this->name,
            'nisn' => $this->nisn,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'extracurricular_position' => $this->extracurricular_position,
            'extracurricular_activeness' => $this->extracurricular_activeness,
            'class_attendance_percentage' => $this->class_attendance_percentage,
            'average_score' => $this->average_score,
            'tuition_payment_delays' => $this->tuition_payment_delays,
        ]);

        session()->flash('message', 'Student successfully created.');

        $this->redirectRoute('teacher.students.index', navigate: true); // Assuming you have a route named 'teacher.students.index' for the list
    }

    public function render(): View
    {
        return view('livewire.teacher.students.create-student');
    }
}
