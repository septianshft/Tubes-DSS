<?php

namespace App\Livewire\Teacher\Students;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse; // Added import
use Illuminate\Routing\Redirector; // Added import
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException; // Added
use Illuminate\Support\Facades\Gate; // Added Gate import

class EditStudent extends Component
{
    use AuthorizesRequests; // Added

    public Student $student;
    public string $name = '';
    public ?string $nisn = null;
    public ?string $date_of_birth = null;
    public ?string $address = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $extracurricular_position = null;
    public ?int $extracurricular_activeness = null;
    public ?int $class_attendance_percentage = null;
    public ?float $average_score = null;
    public ?int $tuition_payment_delays = null;

    public Collection $classes;
    public bool $showSuccessIndicator = false;

    // Add boot method to initialize $classes
    public function boot(): void
    {
        $this->classes = new Collection();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|size:10|unique:students,nisn,' . $this->student->id,
            'email' => 'required|email|unique:students,email,' . $this->student->id,
            'phone' => 'nullable|string|min:10|max:15',
            'address' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'extracurricular_position' => 'nullable|string|max:255',
            'extracurricular_activeness' => 'nullable|integer|min:0|max:100',
            'class_attendance_percentage' => 'nullable|numeric|min:0|max:100',
            'average_score' => 'nullable|numeric|min:0|max:100',
            'tuition_payment_delays' => 'nullable|integer|min:0',
        ];
    }

    public function mount(Student $student): void
    {
        $this->authorize('update', $student); // Changed
        $this->student = $student;
        $this->name = $student->name;
        $this->nisn = $student->nisn;
        $this->date_of_birth = $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : null;
        $this->address = $student->address;
        $this->email = $student->email;
        $this->phone = $student->phone;
        $this->extracurricular_position = $student->extracurricular_position;
        $this->extracurricular_activeness = $student->extracurricular_activeness;
        $this->class_attendance_percentage = $student->class_attendance_percentage;
        $this->average_score = $student->average_score;
        $this->tuition_payment_delays = $student->tuition_payment_delays;
    }

    // Was: public function saveStudent(): Redirector|RedirectResponse
    public function update(): Redirector|RedirectResponse
    {
        // Explicitly authorize the update action again
        if (Gate::denies('update', $this->student)) {
            abort(403);
        }

        $this->validate();

        $this->student->update([
            'name' => $this->name,
            'nisn' => $this->nisn,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'extracurricular_position' => $this->extracurricular_position,
            'extracurricular_activeness' => $this->extracurricular_activeness,
            'class_attendance_percentage' => $this->class_attendance_percentage,
            'average_score' => $this->average_score,
            'tuition_payment_delays' => $this->tuition_payment_delays,
        ]);

        session()->flash('success', 'Student updated successfully.');

        return redirect()->route('teacher.students.index');
    }

    public function render(): View
    {
        return view('livewire.teacher.students.edit-student', [
            'student' => $this->student,
        ]);
    }
}
