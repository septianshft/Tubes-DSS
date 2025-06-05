<?php

namespace App\Livewire\Admin\ScholarshipBatches;

use App\Models\ScholarshipBatch;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class ListScholarshipBatches extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    // Bulk Actions
    public array $selectedBatches = [];
    public bool $selectAll = false;

    // Modal States
    public $batchIdToClose;
    public bool $confirmingCloseBatch = false;
    public $batchIdToActivate;
    public bool $confirmingActivateBatch = false;
    public $batchesToDelete = [];
    public bool $confirmingBulkDelete = false;

    // Statistics
    public array $statistics = [];

    public function mount()
    {
        $this->calculateStatistics();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedBatches = $this->getBatches()->pluck('id')->toArray();
        } else {
            $this->selectedBatches = [];
        }
    }

    public function toggleBatchSelection($batchId)
    {
        if (in_array($batchId, $this->selectedBatches)) {
            $this->selectedBatches = array_filter($this->selectedBatches, fn($id) => $id !== $batchId);
        } else {
            $this->selectedBatches[] = $batchId;
        }

        $this->selectAll = count($this->selectedBatches) === $this->getBatches()->count();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function getBatches()
    {
        $query = ScholarshipBatch::withCount(['submissions', 'submissions as pending_submissions_count' => function ($query) {
            $query->where('status', 'pending');
        }, 'submissions as approved_submissions_count' => function ($query) {
            $query->where('status', 'approved');
        }]);

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            if ($this->statusFilter === 'computed_active') {
                $query->whereDate('start_date', '<=', Carbon::now())
                      ->whereDate('end_date', '>=', Carbon::now());
            } elseif ($this->statusFilter === 'computed_upcoming') {
                $query->whereDate('start_date', '>', Carbon::now());
            } elseif ($this->statusFilter === 'computed_expired') {
                $query->whereDate('end_date', '<', Carbon::now());
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        // Apply sorting
        if ($this->sortBy === 'submissions_count') {
            $query->orderBy('submissions_count', $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.scholarship-batches.list-scholarship-batches', [
            'batches' => $this->getBatches(),
            'statistics' => $this->statistics,
        ]);
    }

    public function calculateStatistics()
    {
        $this->statistics = [
            'total_batches' => ScholarshipBatch::count(),
            'active_batches' => ScholarshipBatch::whereDate('start_date', '<=', Carbon::now())
                                              ->whereDate('end_date', '>=', Carbon::now())
                                              ->count(),
            'upcoming_batches' => ScholarshipBatch::whereDate('start_date', '>', Carbon::now())->count(),
            'closed_batches' => ScholarshipBatch::where('status', 'closed')->count(),
            'total_submissions' => ScholarshipBatch::withCount('submissions')->get()->sum('submissions_count'),
        ];
    }

    public function deleteBatch($batchId)
    {
        $batch = ScholarshipBatch::findOrFail($batchId);
        $batchName = $batch->name;
        $batch->delete();
        session()->flash('message', "Scholarship batch \"{$batchName}\" deleted successfully.");
        $this->calculateStatistics();
    }

    public function confirmCloseBatch($batchId)
    {
        $this->batchIdToClose = $batchId;
        $this->confirmingCloseBatch = true;
    }

    public function closeBatch()
    {
        if ($this->batchIdToClose) {
            $batch = ScholarshipBatch::findOrFail($this->batchIdToClose);
            $batch->status = 'closed';
            $batch->save();

            // Automatically reject pending/under_review submissions for this batch
            $batch->submissions()
                ->whereIn('status', ['pending', 'under_review'])
                ->update(['status' => 'rejected']);

            session()->flash('message', 'Scholarship batch "' . $batch->name . '" closed successfully. Pending submissions have been rejected.');
            $this->calculateStatistics();
        } else {
            session()->flash('error', 'Could not close batch. Batch ID not specified.');
        }
        $this->resetCloseConfirmation();
    }

    public function resetCloseConfirmation()
    {
        $this->confirmingCloseBatch = false;
        $this->batchIdToClose = null;
    }

    public function confirmActivateBatch($batchId)
    {
        $this->batchIdToActivate = $batchId;
        $this->confirmingActivateBatch = true;
    }

    public function activateBatch()
    {
        if ($this->batchIdToActivate) {
            $batch = ScholarshipBatch::findOrFail($this->batchIdToActivate);

            // Set status to active and adjust dates if needed
            $batch->status = 'active';

            // If start date is in the future, adjust it to today
            if ($batch->start_date->isFuture()) {
                $batch->start_date = Carbon::now();
                // Ensure end date is after start date
                if ($batch->end_date->lte($batch->start_date)) {
                    $batch->end_date = Carbon::now()->addDays(30); // Default 30 days duration
                }
            }

            $batch->save();

            session()->flash('message', 'Scholarship batch "' . $batch->name . '" activated successfully.');
            $this->calculateStatistics();
        } else {
            session()->flash('error', 'Could not activate batch. Batch ID not specified.');
        }
        $this->resetActivateConfirmation();
    }

    public function resetActivateConfirmation()
    {
        $this->confirmingActivateBatch = false;
        $this->batchIdToActivate = null;
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedBatches)) {
            session()->flash('error', 'No batches selected for deletion.');
            return;
        }
        $this->batchesToDelete = $this->selectedBatches;
        $this->confirmingBulkDelete = true;
    }

    public function bulkDelete()
    {
        if (!empty($this->batchesToDelete)) {
            $count = count($this->batchesToDelete);
            ScholarshipBatch::whereIn('id', $this->batchesToDelete)->delete();
            session()->flash('message', "{$count} scholarship batches deleted successfully.");
            $this->selectedBatches = [];
            $this->selectAll = false;
            $this->calculateStatistics();
        }
        $this->resetBulkDeleteConfirmation();
    }

    public function resetBulkDeleteConfirmation()
    {
        $this->confirmingBulkDelete = false;
        $this->batchesToDelete = [];
    }

    public function bulkClose()
    {
        if (empty($this->selectedBatches)) {
            session()->flash('error', 'No batches selected for closing.');
            return;
        }

        $count = 0;
        foreach ($this->selectedBatches as $batchId) {
            $batch = ScholarshipBatch::find($batchId);
            if ($batch && $batch->status !== 'closed') {
                $batch->status = 'closed';
                $batch->save();

                // Automatically reject pending submissions
                $batch->submissions()
                    ->whereIn('status', ['pending', 'under_review'])
                    ->update(['status' => 'rejected']);
                $count++;
            }
        }

        session()->flash('message', "{$count} scholarship batches closed successfully.");
        $this->selectedBatches = [];
        $this->selectAll = false;
        $this->calculateStatistics();
    }

    public function bulkActivate()
    {
        if (empty($this->selectedBatches)) {
            session()->flash('error', 'No batches selected for activation.');
            return;
        }

        $count = 0;
        $today = Carbon::now();

        foreach ($this->selectedBatches as $batchId) {
            $batch = ScholarshipBatch::find($batchId);
            if ($batch && $batch->status !== 'active') {
                $batch->status = 'active';

                // Adjust dates if needed
                if ($batch->start_date->isFuture()) {
                    $batch->start_date = $today;
                    if ($batch->end_date->lte($batch->start_date)) {
                        $batch->end_date = $today->copy()->addDays(30);
                    }
                }

                $batch->save();
                $count++;
            }
        }

        session()->flash('message', "{$count} scholarship batches activated successfully.");
        $this->selectedBatches = [];
        $this->selectAll = false;
        $this->calculateStatistics();
    }

    public function clearSelection()
    {
        $this->selectedBatches = [];
        $this->selectAll = false;
    }
}
