<?php

namespace App\Livewire\Teacher;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')] // Assuming you have a default app layout
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.teacher.dashboard');
    }
}
