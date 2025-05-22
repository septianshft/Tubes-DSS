<?php

namespace App\Livewire\Admin\Submissions;

use App\Models\StudentSubmission;
use App\Models\ScholarshipBatch;
use App\Services\SAWCalculatorService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

#[Layout('components.layouts.app')]
class ViewSubmissions extends Component
{
    use WithPagination;

    public ScholarshipBatch $batch;
    public string $searchTerm = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at'; // Default sort, will be updated in mount
    public string $sortDirection = 'desc';
    protected SAWCalculatorService $sawCalculatorService;

    public function boot(SAWCalculatorService $sawCalculatorService)
    {
        $this->sawCalculatorService = $sawCalculatorService;
    }

    public function mount(ScholarshipBatch $batch)
    {
        $this->batch = $batch;
        if ($this->batch->criteria_config && !empty($this->batch->criteria_config)) {
            $this->sortBy = 'final_saw_score'; // Changed from 'saw_score'
            $this->sortDirection = 'desc';
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = ($field === 'final_saw_score') ? 'desc' : 'asc'; // Changed from 'saw_score'
        }
        $this->resetPage();
    }

    public function render()
    {
        $query = StudentSubmission::query()
            ->where('scholarship_batch_id', $this->batch->id)
            ->with(['student']);

        if ($this->searchTerm) {
            $query->whereHas('student', function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('nisn', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $submissionsCollection = $query->get();

        if ($submissionsCollection->isNotEmpty() && !empty($this->batch->criteria_config)) {
            Log::info("[ViewSubmissions] Attempting SAW calculation for Batch ID {$this->batch->id}", [
                'batch_id' => $this->batch->id,
                'submissions_count' => $submissionsCollection->count(),
                'criteria_config_present' => !empty($this->batch->criteria_config)
            ]);
            try {
                $this->sawCalculatorService->calculateScoresForBatch($this->batch, $submissionsCollection);
                Log::info("[ViewSubmissions] SAW calculation completed for Batch ID {$this->batch->id}. First submission final_saw_score: " . ($submissionsCollection->first()->final_saw_score ?? 'N/A'), [
                    'batch_id' => $this->batch->id,
                    'first_submission_all_props' => $submissionsCollection->isNotEmpty() ? $submissionsCollection->first()->toArray() : null
                ]);

                // Persist the calculated scores
                foreach ($submissionsCollection as $submission) {
                    if ($submission->isDirty(['normalized_scores', 'final_saw_score'])) {
                        $submission->save();
                    }
                }
                Log::info("[ViewSubmissions] Persisted scores for Batch ID {$this->batch->id}");

                // Calculate ranks
                $rankedSubmissions = $submissionsCollection->sortByDesc('final_saw_score');
                $currentRank = 1;
                $previousScore = null;
                foreach ($rankedSubmissions as $key => $rankedSubmission) {
                    if ($previousScore !== null && $rankedSubmission->final_saw_score < $previousScore) {
                        $currentRank++;
                    }
                    // Find the original submission in the collection and update its rank
                    $originalSubmission = $submissionsCollection->firstWhere('id', $rankedSubmission->id);
                    if ($originalSubmission) {
                        $originalSubmission->rank = $currentRank;
                    }
                    $previousScore = $rankedSubmission->final_saw_score;
                }
                Log::info("[ViewSubmissions] Calculated ranks for Batch ID {$this->batch->id}. First submission rank: " . ($submissionsCollection->first()->rank ?? 'N/A'));


            } catch (\Exception $e) {
                Log::error("[ViewSubmissions] SAW Calculation Error for Batch ID {$this->batch->id}: " . $e->getMessage(), [
                    'batch_id' => $this->batch->id,
                    'error_message' => $e->getMessage(),
                    'trace_preview' => substr($e->getTraceAsString(), 0, 500) // Log a preview of the trace
                ]);
                session()->flash('error', 'Could not calculate DSS scores. Error: ' . $e->getMessage());
                $submissionsCollection->each(function ($submission) {
                    $submission->saw_score = null;
                    $submission->rank = null;
                });
            }
        } else {
            Log::info("[ViewSubmissions] Skipping SAW calculation for Batch ID {$this->batch->id}", [
                'batch_id' => $this->batch->id,
                'submissions_count' => $submissionsCollection->count(),
                'criteria_config_present' => !empty($this->batch->criteria_config),
                'criteria_config_content_is_array' => is_array($this->batch->criteria_config),
                'criteria_config_empty' => empty($this->batch->criteria_config)
            ]);
            $submissionsCollection->each(function ($submission) {
                $submission->saw_score = null;
                $submission->rank = null;
            });
        }

        if ($this->sortBy === 'final_saw_score') { // Changed from 'saw_score'
            if ($this->sortDirection === 'desc') {
                $submissionsCollection = $submissionsCollection->sortByDesc(function($submission) {
                    return $submission->final_saw_score ?? -INF; // Changed from saw_score
                });
            } else {
                $submissionsCollection = $submissionsCollection->sortBy(function($submission) {
                    return $submission->final_saw_score ?? INF; // Changed from saw_score
                });
            }
        } elseif ($this->sortBy === 'student.name') {
            $submissionsCollection = $submissionsCollection->sortBy(function($submission) {
                return optional($submission->student)->name ?? '';
            }, SORT_REGULAR, $this->sortDirection === 'desc');
        } elseif ($this->sortBy === 'student.nim') {
             $submissionsCollection = $submissionsCollection->sortBy(function($submission) {
                return optional($submission->student)->nisn ?? '';
            }, SORT_REGULAR, $this->sortDirection === 'desc');
        } elseif ($this->sortBy) {
            $submissionsCollection = $submissionsCollection->sortBy($this->sortBy, SORT_REGULAR, $this->sortDirection === 'desc');
        }

        $page = Paginator::resolveCurrentPage('page');
        $perPage = 10;
        $paginatedSubmissions = new LengthAwarePaginator(
            $submissionsCollection->forPage($page, $perPage),
            $submissionsCollection->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page']
        );

        return view('livewire.admin.submissions.view-submissions', [
            'submissions' => $paginatedSubmissions,
        ]);
    }
}
