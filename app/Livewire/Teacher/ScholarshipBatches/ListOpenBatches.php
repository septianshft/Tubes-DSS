<?php

namespace App\Livewire\Teacher\ScholarshipBatches;

use App\Models\ScholarshipBatch;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; // Added for teacher context if needed in future

class ListOpenBatches extends Component
{
    use WithPagination;

    public function render(): View
    {
        // Fetch batches that are open, upcoming, or closed for teacher view
        $batches = ScholarshipBatch::whereIn('status', ['open', 'upcoming', 'closed'])
            ->orderByRaw("
                CASE status
                    WHEN 'open' THEN 1
                    WHEN 'upcoming' THEN 2
                    WHEN 'closed' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('start_date', 'desc') // Secondary sort: newest start dates first within status groups
            ->paginate(10);

        return view('livewire.teacher.scholarship-batches.list-open-batches', [
            'batches' => $batches,
        ]);
    }
}
