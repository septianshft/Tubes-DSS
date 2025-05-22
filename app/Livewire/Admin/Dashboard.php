<?php

namespace App\Livewire\Admin;

use App\Models\ScholarshipBatch;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\View\View;
use Illuminate\Support\Collection;

#[Layout('components.layouts.app')] // Assuming you have a default app layout
class Dashboard extends Component
{
    public Collection $activeBatches;
    public Collection $closedBatches;
    public int $totalActiveBatches = 0;
    public int $totalClosedBatches = 0;

    public function mount(): void
    {
        $this->activeBatches = ScholarshipBatch::where('status', 'open')
                                ->withCount('submissions') // Count related submissions
                                ->orderBy('created_at', 'desc')
                                ->get();
        $this->totalActiveBatches = $this->activeBatches->count();

        $this->closedBatches = ScholarshipBatch::where('status', 'closed')
                                ->withCount('submissions')
                                ->orderBy('end_date', 'desc') // Show most recently closed first
                                ->take(5) // Limit to show a few recent ones
                                ->get();
        $this->totalClosedBatches = ScholarshipBatch::where('status', 'closed')->count(); // Total count might be different from displayed
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard');
    }
}
