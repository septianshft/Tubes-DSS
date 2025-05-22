<?php

namespace App\Livewire\Teacher\Submissions;

use App\Models\StudentSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;

class ListSubmissions extends Component
{
    use WithPagination;

    public string $statusFilter = ''; // '' for All, or specific status like 'pending', 'approved', 'rejected'

    protected $queryString = [
        'statusFilter' => ['except' => '', 'as' => 'status'],
    ];

    public function render(): View
    {
        $query = StudentSubmission::where('submitted_by_teacher_id', Auth::id())
            ->with(['scholarshipBatch', 'student']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $submissions = $query->latest()->paginate(10);

        return view('livewire.teacher.submissions.list-submissions', [
            'submissions' => $submissions,
        ]);
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage(); // Reset pagination when filter changes
    }
}
