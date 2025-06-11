<?php

namespace App\Livewire\Teacher\Submissions;

use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\StudentSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

#[Layout('components.layouts.app')]
class CreateStudentSubmissionForBatch extends Component
{
    public ScholarshipBatch $batch;
    public array $selectedStudentIds = [];
    public ?Collection $allStudents = null; // Initialize as null or empty Collection
    public string $studentSearch = '';
    public array $studentCriteriaValues = []; // Renamed from criteriaValues and changed structure
    public array $criteriaConfig = [];

    protected function isBatchEffectivelyOpen(ScholarshipBatch $batch): bool
    {
        $today = Carbon::now()->startOfDay();
        $startDate = $batch->start_date ? Carbon::parse($batch->start_date)->startOfDay() : null;
        $endDate = $batch->end_date ? Carbon::parse($batch->end_date)->endOfDay() : null;

        if ($batch->status === 'open' && ($endDate === null || !$today->isAfter($endDate))) {
            return true;
        }

        if ($batch->status === 'upcoming' && $startDate && $endDate && $today->isBetween($startDate, $endDate)) {
            return true;
        }

        return false;
    }

    public function mount(ScholarshipBatch $batch): void
    {
        if (!$this->isBatchEffectivelyOpen($batch)) {
            session()->flash('error', 'This scholarship batch is not currently open for submissions.');
            $this->redirectRoute('teacher.scholarship-batches.open', navigate: true);
            return;
        }

        $this->batch = $batch;
        $this->criteriaConfig = $this->batch->criteria_config ?? [];
        $this->allStudents = Student::where('teacher_id', Auth::id())->orderBy('name')->get();
        // Ensure selectedStudentIds is an array, even if empty, to prevent issues.
        if (!is_array($this->selectedStudentIds)) {
            $this->selectedStudentIds = [];
        }
        $this->updatedSelectedStudentIds(); // Call to initialize criteria for any pre-selected students (if any)
    }

    // Lifecycle hook for when selectedStudentIds property is updated
    public function updatedSelectedStudentIds(): void
    {
        $validatedAndNormalizedIds = [];
        if (is_array($this->selectedStudentIds)) {
            $validatedAndNormalizedIds = array_map('intval', array_filter($this->selectedStudentIds, function($value) {
                return is_numeric($value) && $value !== ''; // Ensure it's numeric and not an empty string
            }));
        } else {
            // This case handles if $this->selectedStudentIds is not an array (e.g. null or string)
            $this->selectedStudentIds = []; // Normalize to empty array
        }

        $this->selectedStudentIds = $validatedAndNormalizedIds;

        $currentStudentIdsWithCriteria = array_keys($this->studentCriteriaValues);

        // Add criteria for newly selected students
        $idsToAdd = array_diff($this->selectedStudentIds, $currentStudentIdsWithCriteria);
        foreach ($idsToAdd as $studentId) {
            $this->initializeCriteriaForStudent((int)$studentId); // Ensure $studentId is int
        }

        // Remove criteria for deselected students
        $idsToRemove = array_diff($currentStudentIdsWithCriteria, $this->selectedStudentIds);
        foreach ($idsToRemove as $studentId) {
            unset($this->studentCriteriaValues[(int)$studentId]); // Ensure $studentId is int
        }
    }

    protected function initializeCriteriaForStudent(int $studentId): void
    {
        $this->studentCriteriaValues[$studentId] = [];
        $student = $this->allStudents->find($studentId);

        foreach ($this->criteriaConfig as $criterion) {
            if (isset($criterion['id'])) {
                $defaultValue = ''; // Default empty value
                // Attempt to pre-fill from student model if key is set
                if ($student && isset($criterion['student_model_key'])) {
                    $modelKey = $criterion['student_model_key'];
                    if (property_exists($student, $modelKey) && $student->{$modelKey} !== null) {
                        $defaultValue = $student->{$modelKey};
                    }
                }
                $this->studentCriteriaValues[$studentId][$criterion['id']] = $defaultValue;
            }
        }
    }

    // Method to deselect a student (used by the pills UI)
    public function deselectStudent(int $studentId): void
    {
        $this->selectedStudentIds = array_filter($this->selectedStudentIds, fn($id) => (int)$id != $studentId); // Cast $id to int for comparison
        $this->selectedStudentIds = array_map('intval', array_values($this->selectedStudentIds)); // Re-index and ensure int
        unset($this->studentCriteriaValues[$studentId]); // Remove their criteria values
    }

    // Renamed and repurposed: now clears all student-specific criteria values upon successful submission or full reset
    protected function resetAllStudentCriteriaValues(): void
    {
        $this->studentCriteriaValues = [];
        // Optionally, re-initialize for currently selected students if needed after a reset, but typically this is for a full clear.
    }

    protected function rules(): array
    {
        $rules = [
            'selectedStudentIds' => 'required|array|min:1',
            'selectedStudentIds.*' => 'required|exists:students,id',
        ];

        foreach ($this->selectedStudentIds as $studentId) {
            foreach ($this->criteriaConfig as $criterion) {
                if (isset($criterion['id'])) {
                    $ruleKey = 'studentCriteriaValues.' . $studentId . '.' . $criterion['id'];
                    $rules[$ruleKey] = $criterion['rules'] ?? 'required';
                }
            }
        }
        return $rules;
    }

    protected function messages(): array
    {
        $messages = [
            'selectedStudentIds.required' => 'Please select at least one student.',
            'selectedStudentIds.min' => 'Please select at least one student.',
            'selectedStudentIds.*.exists' => 'One or more selected students are invalid.',
        ];

        foreach ($this->selectedStudentIds as $studentId) {
            $student = $this->allStudents->find($studentId);
            $studentName = $student ? $student->name : "Student ID {$studentId}";
            foreach ($this->criteriaConfig as $criterion) {
                if (isset($criterion['id']) && isset($criterion['name'])) {
                    $messageKey = 'studentCriteriaValues.' . $studentId . '.' . $criterion['id'] . '.required';
                    $messages[$messageKey] = "The '{$criterion['name']}' field for {$studentName} is required.";
                    // Add other custom messages for different rules if needed
                }
            }
        }
        return $messages;
    }

    // Add a method to check if form can be submitted
    public function checkFormReady(): bool
    {
        if (empty($this->selectedStudentIds)) {
            return false;
        }

        foreach ($this->selectedStudentIds as $studentId) {
            foreach ($this->criteriaConfig as $criterion) {
                if (isset($criterion['id'])) {
                    $value = $this->studentCriteriaValues[$studentId][$criterion['id']] ?? null;
                    $isRequired = ($criterion['rules'] ?? 'required') === 'required' || str_contains($criterion['rules'] ?? '', 'required');

                    if ($isRequired && ($value === null || $value === '')) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function saveSubmission(): void
    {
        if (!$this->isBatchEffectivelyOpen($this->batch)) {
            session()->flash('error', 'This scholarship batch is no longer open for submissions.');
            $this->redirectRoute('teacher.scholarship-batches.open', navigate: true);
            return;
        }
        // Ensure selectedStudentIds contains integers before validation and processing
        $this->selectedStudentIds = array_map('intval', array_filter($this->selectedStudentIds, 'is_numeric'));


        $this->validate();

        $submittedCount = 0;
        $alreadySubmittedCount = 0;
        $errorMessages = [];

        foreach ($this->selectedStudentIds as $studentId) {
            $student = $this->allStudents->find($studentId);
            $studentName = $student ? $student->name : "ID {$studentId}";

            $existingSubmission = StudentSubmission::where('student_id', $studentId)
                ->where('scholarship_batch_id', $this->batch->id)
                ->exists();

            if ($existingSubmission) {
                $errorMessages[] = "Student '{$studentName}' has already been submitted for this batch.";
                $alreadySubmittedCount++;
                continue;
            }

            // Get the specific criteria for this student
            $rawCriteriaForStudent = $this->studentCriteriaValues[$studentId] ?? [];
            $validCriteriaValues = [];
            foreach($this->criteriaConfig as $criterionConf) {
                if (isset($criterionConf['id']) && array_key_exists($criterionConf['id'], $rawCriteriaForStudent)) {
                    $validCriteriaValues[$criterionConf['id']] = $rawCriteriaForStudent[$criterionConf['id']];
                }
            }

            StudentSubmission::create([
                'scholarship_batch_id' => $this->batch->id,
                'student_id' => $studentId,
                'submitted_by_teacher_id' => Auth::id(),
                'raw_criteria_values' => $validCriteriaValues, // Use student-specific criteria
                'status' => 'pending',
                'submission_date' => now(),
            ]);
            $submittedCount++;
        }

        $flashMessage = '';
        $flashType = 'message';

        if ($submittedCount > 0) {
            $flashMessage .= "{$submittedCount} student(s) submitted successfully. ";
        }
        if ($alreadySubmittedCount > 0) {
            $flashMessage .= "{$alreadySubmittedCount} student(s) were already submitted. ";
        }
        if (!empty($errorMessages) && $submittedCount === 0) {
             $specificErrors = array_filter($errorMessages, fn($msg) => !str_contains($msg, 'already submitted'));
             if (!empty($specificErrors)) {
                $flashMessage .= implode(' ', $specificErrors);
             } else if ($alreadySubmittedCount > 0 && empty($specificErrors)) {
                // Only "already submitted" messages
             } else {
                $flashMessage = implode(' ', $errorMessages);
             }
             $flashType = 'error';
        } else if (!empty($errorMessages) && $submittedCount > 0) {
            // Summary is already in flashMessage.
        }

        if (empty(trim($flashMessage))) {
            $flashMessage = 'No new students were submitted.';
            if ($alreadySubmittedCount > 0 && $submittedCount === 0) {
                 $flashMessage = "{$alreadySubmittedCount} student(s) were already submitted. No new students submitted.";
                 $flashType = 'message';
            } else {
                $flashType = 'error';
            }
        }

        session()->flash($flashType, trim($flashMessage));

        if ($submittedCount > 0 || ($alreadySubmittedCount > 0 && $submittedCount == 0 && empty(array_filter($errorMessages, fn($msg) => !str_contains($msg, 'already submitted')))) ) {
            $this->selectedStudentIds = [];
            $this->studentCriteriaValues = []; // Clear all student criteria
            $this->studentSearch = ''; // Reset search
        }

        $this->redirectRoute('teacher.scholarship-batches.open', navigate: true);
    }

    public function render(): View
    {
        $studentsQuery = Student::where('teacher_id', Auth::id());

        if (!empty($this->studentSearch)) {
            $studentsQuery->where(function ($query) {
                $query->where('name', 'like', '%'.$this->studentSearch.'%')
                      ->orWhere('nisn', 'like', '%'.$this->studentSearch.'%');
            });
        }

        $students = $studentsQuery->orderBy('name')->get();

        return view('livewire.teacher.submissions.create-student-submission-for-batch', [
            'students' => $students,
        ]);
    }
}
