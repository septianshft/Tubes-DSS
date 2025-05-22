<?php

namespace App\Livewire\Admin\ScholarshipBatches;

use App\Models\ScholarshipBatch;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout; // Import the Layout attribute

#[Layout('components.layouts.app')] // Specify the layout using the attribute
class ListScholarshipBatches extends Component
{
    use WithPagination;

    public $batchIdToClose;
    public $confirmingCloseBatch = false;

    public function mount()
    {
        // Optional: Gate check for initial access if needed, though route middleware handles it
        // if (Gate::denies('viewAny', ScholarshipBatch::class)) {
        //     abort(403);
        // }
    }

    public function render()
    {
        return view('livewire.admin.scholarship-batches.list-scholarship-batches', [
            'batches' => ScholarshipBatch::withCount('submissions')->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }

    public function deleteBatch($batchId)
    {
        $batch = ScholarshipBatch::findOrFail($batchId);
        // Optional: Gate check for delete
        // if (Gate::denies('delete', $batch)) {
        //     session()->flash('error', 'You do not have permission to delete this batch.');
        //     return;
        // }
        $batch->delete();
        session()->flash('message', 'Scholarship batch deleted successfully.');
    }

    public function confirmCloseBatch($batchId)
    {
        $this->batchIdToClose = $batchId;
        $this->confirmingCloseBatch = true;
        // It might be good to dispatch a browser event to show a modal,
        // or use a Livewire modal component for confirmation.
        // For now, we'll add a simple confirmation property and handle it in the view or a subsequent action.
        // This example assumes a more direct close for simplicity, or that the view handles the modal.
        // A more robust solution would use a dedicated modal for confirmation.
        $this->closeBatch(); // Directly calling close for now, ideally show a modal first.
    }

    public function closeBatch()
    {
        if ($this->batchIdToClose) {
            $batch = ScholarshipBatch::findOrFail($this->batchIdToClose);
            // Optional: Gate check for closing
            // if (Gate::denies('close', $batch)) {
            //     session()->flash('error', 'You do not have permission to close this batch.');
            //     $this->resetCloseConfirmation();
            //     return;
            // }
            $batch->status = 'closed';
            $batch->save();

            // Automatically reject pending/under_review submissions for this batch
            $batch->submissions()
                ->whereIn('status', ['pending', 'under_review'])
                ->update(['status' => 'rejected']);

            session()->flash('message', 'Scholarship batch "' . $batch->name . '" closed successfully. Pending submissions have been rejected.');
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
}
