<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')] // Assuming you have a default app layout
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
