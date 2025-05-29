<?php

namespace App\Livewire\Admin\Results;

use App\Models\ScholarshipBatch;
use App\Models\StudentSubmission;
use App\Services\SAWCalculatorService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class ScholarshipResults extends Component
{
    use WithPagination;

    public ScholarshipBatch $batch;
    public string $statusFilter = '';
    public int $resultsPerPage = 20;
    public bool $showStatistics = true;

    // Statistics properties
    public int $totalSubmissions = 0;
    public int $approvedCount = 0;
    public int $rejectedCount = 0;
    public int $pendingCount = 0;
    public float $averageScore = 0;
    public float $highestScore = 0;
    public float $lowestScore = 0;
    public int $quota = 0;
    public int $remainingSlots = 0;

    // Selection properties
    public array $selectedStudentIds = [];
    public bool $showSelectionModal = false;
    public int $autoSelectCount = 0;

    protected SAWCalculatorService $sawCalculatorService;

    public function boot(SAWCalculatorService $sawCalculatorService)
    {
        $this->sawCalculatorService = $sawCalculatorService;
    }

    public function mount(ScholarshipBatch $batch)
    {
        $this->batch = $batch;
        $this->quota = $batch->quota ?? 0;
        $this->calculateStatistics();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedResultsPerPage()
    {
        $this->resetPage();
    }

    protected function calculateStatistics(): void
    {
        $submissions = StudentSubmission::where('scholarship_batch_id', $this->batch->id)->get();

        $this->totalSubmissions = $submissions->count();
        $this->approvedCount = $submissions->where('status', 'approved')->count();
        $this->rejectedCount = $submissions->where('status', 'rejected')->count();
        $this->pendingCount = $submissions->whereIn('status', ['pending', 'need_revision'])->count();
        $this->remainingSlots = max(0, $this->quota - $this->approvedCount);

        if ($submissions->isNotEmpty()) {
            $scores = $submissions->whereNotNull('final_saw_score')->pluck('final_saw_score');

            if ($scores->isNotEmpty()) {
                $this->averageScore = round($scores->avg(), 4);
                $this->highestScore = round($scores->max(), 4);
                $this->lowestScore = round($scores->min(), 4);
            }
        }
    }

    public function refreshScores(): void
    {
        if (!$this->batch->criteria_config || empty($this->batch->criteria_config)) {
            session()->flash('error', 'Cannot calculate scores: No criteria configured for this batch.');
            return;
        }

        try {
            $submissions = StudentSubmission::where('scholarship_batch_id', $this->batch->id)->get();

            if ($submissions->isEmpty()) {
                session()->flash('warning', 'No submissions found to calculate scores for.');
                return;
            }

            // Recalculate all scores
            $processedSubmissions = $this->sawCalculatorService->calculateScoresForBatch($this->batch, $submissions);

            // Persist the updated scores
            foreach ($processedSubmissions as $submission) {
                if ($submission->isDirty(['normalized_scores', 'final_saw_score'])) {
                    $submission->save();
                }
            }

            // Recalculate rankings
            $this->updateRankings();

            // Refresh statistics
            $this->calculateStatistics();

            session()->flash('message', 'Scores and rankings have been successfully refreshed for all submissions.');
        } catch (\Exception $e) {
            Log::error("Error refreshing scores for batch {$this->batch->id}: " . $e->getMessage());
            session()->flash('error', 'Error refreshing scores: ' . $e->getMessage());
        }
    }

    protected function updateRankings(): void
    {
        $submissions = StudentSubmission::where('scholarship_batch_id', $this->batch->id)
            ->whereNotNull('final_saw_score')
            ->orderByDesc('final_saw_score')
            ->get();

        $currentRank = 1;
        $previousScore = null;
        $sameRankCount = 0;

        foreach ($submissions as $index => $submission) {
            if ($previousScore !== null && $submission->final_saw_score < $previousScore) {
                $currentRank += $sameRankCount + 1;
                $sameRankCount = 0;
            } elseif ($previousScore !== null && $submission->final_saw_score == $previousScore) {
                $sameRankCount++;
            }

            $submission->rank = $currentRank;
            $submission->save();

            $previousScore = $submission->final_saw_score;
        }
    }

    public function autoSelectTopCandidates(): void
    {
        if ($this->autoSelectCount <= 0) {
            session()->flash('error', 'Please specify a valid number of candidates to select.');
            return;
        }

        if ($this->autoSelectCount > $this->remainingSlots) {
            session()->flash('error', "Cannot select {$this->autoSelectCount} candidates. Only {$this->remainingSlots} slots remaining.");
            return;
        }

        $topCandidates = StudentSubmission::where('scholarship_batch_id', $this->batch->id)
            ->where('status', 'pending')
            ->whereNotNull('final_saw_score')
            ->orderByDesc('final_saw_score')
            ->orderBy('id') // Tie breaker: earlier submissions win
            ->limit($this->autoSelectCount)
            ->get();

        if ($topCandidates->count() < $this->autoSelectCount) {
            session()->flash('warning', "Only {$topCandidates->count()} eligible candidates found (requested {$this->autoSelectCount}).");
        }

        $selectedCount = 0;
        foreach ($topCandidates as $submission) {
            $submission->update([
                'status' => 'approved',
                'status_updated_at' => now(),
                'status_updated_by' => auth()->id(),
            ]);
            $selectedCount++;
        }

        $this->calculateStatistics();
        session()->flash('message', "Successfully approved {$selectedCount} top candidates for the scholarship.");
    }

    public function bulkUpdateStatus(string $status, array $submissionIds): void
    {
        if (empty($submissionIds)) {
            return;
        }

        $validStatuses = ['pending', 'approved', 'rejected', 'need_revision'];
        if (!in_array($status, $validStatuses)) {
            session()->flash('error', 'Invalid status provided.');
            return;
        }

        $updated = StudentSubmission::whereIn('id', $submissionIds)
            ->where('scholarship_batch_id', $this->batch->id)
            ->update([
                'status' => $status,
                'status_updated_at' => now(),
                'status_updated_by' => auth()->id(),
            ]);

        $this->calculateStatistics();
        session()->flash('message', "Successfully updated {$updated} submission(s) to {$status}.");
    }

    public function exportResults(): void
    {
        // This will be implemented in a separate method/service
        session()->flash('message', 'Export functionality will be available soon.');
    }    // Test compatibility methods
    public function autoApproveTopCandidates(): void
    {
        // Set the count to remaining slots for auto-approval
        $this->autoSelectCount = $this->remainingSlots;
        $this->autoSelectTopCandidates();
    }

    public function bulkApprove(): void
    {
        $this->bulkUpdateStatus('approved', $this->selectedStudentIds);
        $this->selectedStudentIds = [];
    }

    public function bulkReject(): void
    {
        $this->bulkUpdateStatus('rejected', $this->selectedStudentIds);
        $this->selectedStudentIds = [];
    }

    public function selectAllOnPage(): void
    {
        $this->selectAll();
    }

    public function toggleSelection(int $submissionId): void
    {
        if (in_array($submissionId, $this->selectedStudentIds)) {
            $this->selectedStudentIds = array_filter($this->selectedStudentIds, fn($id) => $id !== $submissionId);
        } else {
            $this->selectedStudentIds[] = $submissionId;
        }
    }

    public function selectAll(): void
    {
        $allSubmissionIds = $this->getSubmissions()->pluck('id')->toArray();
        $this->selectedStudentIds = $allSubmissionIds;
    }

    public function clearSelection(): void
    {
        $this->selectedStudentIds = [];
    }

    protected function getSubmissions()
    {
        $query = StudentSubmission::query()
            ->where('scholarship_batch_id', $this->batch->id)
            ->with(['student']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderByDesc('final_saw_score')
                    ->orderBy('rank')
                    ->orderBy('id')
                    ->paginate($this->resultsPerPage);
    }

    public function render()
    {
        return view('livewire.admin.results.scholarship-results', [
            'submissions' => $this->getSubmissions(),
        ]);
    }
}
