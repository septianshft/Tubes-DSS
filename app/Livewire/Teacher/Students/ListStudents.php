<?php

namespace App\Livewire\Teacher\Students;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;

class ListStudents extends Component
{
    use WithPagination;

    public $search = '';

    public function render(): View
    {
        $students = Student::where('teacher_id', Auth::id())
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('nisn', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        return view('livewire.teacher.students.list-students', [
            'students' => $students,
        ]);
    }
}
