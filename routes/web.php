<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Teacher\Dashboard as TeacherDashboard;
use App\Livewire\Admin\ScholarshipBatches\ListScholarshipBatches;
use App\Livewire\Admin\ScholarshipBatches\CreateScholarshipBatch;
use App\Livewire\Admin\ScholarshipBatches\EditScholarshipBatch;
use App\Livewire\Admin\Submissions\ViewSubmissions;
use App\Livewire\Admin\Submissions\ShowSubmission;
use App\Livewire\Teacher\Students\ListStudents;
use App\Livewire\Teacher\Students\CreateStudent;
use App\Livewire\Teacher\ScholarshipBatches\ListOpenBatches;
use App\Livewire\Teacher\Submissions\ListSubmissions;
use App\Livewire\Teacher\Submissions\CreateStudentSubmissionForBatch;

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

    // Role-specific dashboards and admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/scholarship-batches', ListScholarshipBatches::class)->name('scholarship-batches.index');
        Route::get('/scholarship-batches/create', CreateScholarshipBatch::class)->name('scholarship-batches.create');
        Route::get('/scholarship-batches/{batch}/edit', EditScholarshipBatch::class)->name('scholarship-batches.edit');
        Route::get('scholarship-batches/{batch}/submissions', ViewSubmissions::class)->name('scholarship-batches.submissions');
        Route::get('scholarship-batches/{batch}/submissions/{submission}', ShowSubmission::class)->name('scholarship-batches.submissions.show');
        // Add other admin routes here
    });

    // Teacher specific routes
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', TeacherDashboard::class)->name('dashboard');
        Route::get('/students', ListStudents::class)->name('students.index');
        Route::get('/students/create', CreateStudent::class)->name('students.create');
        Route::get('/scholarship-batches/open', ListOpenBatches::class)->name('scholarship-batches.open');
        Route::get('/scholarship-batches/{batch}/submit-student', CreateStudentSubmissionForBatch::class)->name('submissions.create-for-batch');
        Route::get('/submissions', ListSubmissions::class)->name('submissions.index');
        // Add other teacher routes here, e.g., for managing students
    });
});

require __DIR__.'/auth.php';
