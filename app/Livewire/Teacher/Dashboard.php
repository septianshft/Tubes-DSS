<?php

namespace App\Livewire\Teacher;

use App\Models\ScholarshipBatch;
use App\Models\StudentSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public int $activeSubmissionsCount = 0;
    public int $approvedSubmissionsCount = 0;
    public int $rejectedSubmissionsCount = 0; // Added this line
    public $upcomingBatches; // Will be a collection
    public $recentSubmissions; // Will be a collection

    public function mount(): void
    {
        $teacherId = Auth::id();

        $this->activeSubmissionsCount = StudentSubmission::where('submitted_by_teacher_id', $teacherId)
            ->whereIn('status', ['pending', 'under_review'])
            ->count();

        $this->approvedSubmissionsCount = StudentSubmission::where('submitted_by_teacher_id', $teacherId)
            ->where('status', 'approved')
            ->count();

        // Added this query for rejected submissions
        $this->rejectedSubmissionsCount = StudentSubmission::where('submitted_by_teacher_id', $teacherId)
            ->where('status', 'rejected')
            ->count();

        $today = Carbon::today();
        $this->upcomingBatches = ScholarshipBatch::where(function ($query) use ($today) {
            $query->where('status', 'open')
                  ->where(function ($q) use ($today) {
                      $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $today);
                  });
        })->orWhere(function ($query) use ($today) {
            $query->where('status', 'upcoming')
                  ->where('start_date', '>=', $today);
        })
        ->orderByRaw('CASE WHEN status = \'open\' THEN 0 ELSE 1 END, end_date ASC, start_date ASC') // Prioritize open batches
        ->take(5) // Limit to a reasonable number for the dashboard
        ->get();

        $this->recentSubmissions = StudentSubmission::where('submitted_by_teacher_id', $teacherId)
            ->with(['student', 'scholarshipBatch']) // Eager load relationships
            ->latest('submission_date') // Order by submission date, newest first
            ->take(5)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.teacher.dashboard');
    }
}
