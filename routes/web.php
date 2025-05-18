<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Teacher\Dashboard as TeacherDashboard;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Role-specific dashboards
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    });

    Route::middleware(['role:teacher'])->group(function () {
        Route::get('/teacher/dashboard', TeacherDashboard::class)->name('teacher.dashboard');
    });
});

require __DIR__.'/auth.php';
