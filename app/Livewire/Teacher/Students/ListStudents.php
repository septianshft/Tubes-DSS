<?php

namespace App\Livewire\Teacher\Students;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;

class ListStudents extends Component
{
    use WithPagination;

    public $search = '';

    public function render(): View
    {
        $students = Student::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('nisn', 'like', '%'.$this->search.'%') // Changed 'nis' to 'nisn'
            ->paginate(10);

        return view('livewire.teacher.students.list-students', [
            'students' => $students,
        ]);
    }
}
