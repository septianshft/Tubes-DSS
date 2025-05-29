<?php

namespace App\Livewire\Admin\Submissions;

use App\Models\ScholarshipBatch;
use App\Models\StudentSubmission;
use App\Services\SAWCalculatorService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class ShowSubmission extends Component
{
    public ScholarshipBatch $batch;
    public StudentSubmission $submission;

    public ?float $sawScore = null;
    public array $normalizedScores = []; // Stores normalized scores for each criterion
    public array $criteriaDetails = []; // To store combined details for the view
    public array $sawCalculationSteps = []; // To store the detailed SAW calculation steps

    // Modal and revision notes properties
    public bool $showRevisionModal = false;
    public string $revisionNotes = '';

    protected SAWCalculatorService $sawCalculatorService;

    public function boot(SAWCalculatorService $sawCalculatorService)
    {
        $this->sawCalculatorService = $sawCalculatorService;
    }

    public function mount(ScholarshipBatch $batch, StudentSubmission $submission)
    {
        $this->batch = $batch;
        $this->submission = $submission->load('student');
        Log::debug("[ShowSubmission Mount] Initial submission raw_criteria_values: ", $this->submission->raw_criteria_values ?? ['empty_or_not_set']);

        if ($this->batch->criteria_config && !empty($this->batch->criteria_config)) {
            try {
                // $singleSubmissionCollection = new Collection([$this->submission]); // Old incorrect way
                Log::debug("[ShowSubmission Mount] Submission object BEFORE calling SAWCalculatorService->calculate(): ", ['id' => $this->submission->id, 'raw_criteria_values' => $this->submission->raw_criteria_values, 'normalized_scores' => $this->submission->normalized_scores, 'final_saw_score' => $this->submission->final_saw_score]);

                // Correct: Call the main calculate method, which handles fetching all batch submissions
                $updatedSubmission = $this->sawCalculatorService->calculate($this->submission);

                // Update the component's submission model instance with the processed one
                $this->submission = $updatedSubmission;

                // Log values *after* SAWCalculatorService has run
                Log::debug("[ShowSubmission Mount] Submission object AFTER calling SAWCalculatorService->calculate(): ", ['id' => $this->submission->id, 'raw_criteria_values' => $this->submission->raw_criteria_values, 'normalized_scores' => $this->submission->normalized_scores, 'final_saw_score' => $this->submission->final_saw_score]);

                $this->sawScore = $this->submission->final_saw_score ?? null;
                $this->normalizedScores = $this->submission->normalized_scores ?? []; // This should be populated by the service

                // Capture the calculation details if they exist on the submission object
                if (isset($this->submission->calculation_details)) {
                    $this->sawCalculationSteps = $this->submission->calculation_details;
                    Log::debug("[ShowSubmission Mount] Captured SAW calculation_details: ", $this->sawCalculationSteps);
                } else {
                    $this->sawCalculationSteps = [];
                    Log::debug("[ShowSubmission Mount] SAW calculation_details not found on submission object.");
                }

                Log::debug("[ShowSubmission Mount] Component properties after SAW: ", ['sawScore' => $this->sawScore, 'normalizedScores' => $this->normalizedScores, 'sawCalculationStepsCount' => count($this->sawCalculationSteps)]);

                $this->prepareCriteriaDetails();

            } catch (\Exception $e) { // Corrected: Removed extra backslash
                Log::error("Error calculating SAW score for submission ID {$this->submission->id} in ShowSubmission: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                session()->flash('error', 'Could not retrieve DSS score details for this submission. Error: ' . $e->getMessage());
                $this->sawScore = null;
                $this->normalizedScores = [];
                $this->criteriaDetails = [];
            }
        } else {
            Log::warning("Criteria config not found for batch ID {$this->batch->id} when viewing submission ID {$this->submission->id}.");
            session()->flash('warning', 'DSS scores cannot be displayed as criteria configuration is missing for this batch.');
            $this->criteriaDetails = [];
        }
    }

    protected function prepareCriteriaDetails()
    {
        $this->criteriaDetails = [];
        $submissionRawValues = $this->submission->raw_criteria_values ?? [];

        if (empty($this->batch->criteria_config) || !is_array($this->batch->criteria_config)) {
            Log::warning("[PCD] Criteria config is empty or not an array for batch ID {$this->batch->id} when viewing submission ID {$this->submission->id}.");
            return;
        }
        Log::debug("[PCD] Initial data for submission ID {$this->submission->id}:", [
            'batch_criteria_config' => $this->batch->criteria_config,
            'submission_raw_values' => $submissionRawValues,
            'component_normalized_scores' => $this->normalizedScores
        ]);

        foreach ($this->batch->criteria_config as $criterion) {
            if (!is_array($criterion)) {
                Log::warning("[PCD] Encountered non-array criterion in batch config {$this->batch->id}", ['criterion_data' => $criterion]);
                continue;
            }

            $criterionId = $criterion['id'] ?? null;
            if (!$criterionId) {
                Log::warning("[PCD] Criterion missing ID in batch config {$this->batch->id}", ['criterion' => $criterion]);
                continue;
            }

            Log::debug("[PCD] Processing criterionId: '{$criterionId}'");

            $actualRawValue = null;
            if (array_key_exists($criterionId, $submissionRawValues)) {
                $actualRawValue = $submissionRawValues[$criterionId];
                Log::debug("[PCD] Found raw value for '{$criterionId}': ", [$actualRawValue]);
            } else {
                $actualRawValue = 'N/A_RAW_FALLBACK'; // Distinct fallback if key doesn't exist
                Log::debug("[PCD] Raw value for '{$criterionId}' not found, using fallback: {$actualRawValue}");
            }

            $displayValue = $actualRawValue; // Default display value

            // Handling for qualitative options to get label for displayValue
            if (isset($criterion['data_type']) && $criterion['data_type'] === 'qualitative_option' && $actualRawValue !== 'N/A_RAW_FALLBACK' && isset($criterion['options']) && is_array($criterion['options'])) {
                foreach ($criterion['options'] as $option) {
                    if (is_array($option) && isset($option['value']) && $option['value'] == $actualRawValue && array_key_exists('label', $option)) {
                        $displayValue = $option['label'] ?? $actualRawValue;
                        Log::debug("[PCD] Qualitative option found for '{$criterionId}', displayValue set to label: '{$displayValue}'");
                        break;
                    }
                }
            }
            // Note: qualitative_text display value remains the raw key if mapped, or the raw value itself.

            $actualNormalizedScore = null;
            if (array_key_exists($criterionId, $this->normalizedScores)) {
                $actualNormalizedScore = $this->normalizedScores[$criterionId];
                Log::debug("[PCD] Found normalized value for '{$criterionId}': ", [$actualNormalizedScore]);
            } else {
                $actualNormalizedScore = 'N/A_NORM_FALLBACK'; // Distinct fallback
                Log::debug("[PCD] Normalized value for '{$criterionId}' not found, using fallback: {$actualNormalizedScore}");
            }

            $isActualNormalizedScoreNumeric = is_numeric($actualNormalizedScore);
            Log::debug("[PCD] For '{$criterionId}', actualNormalizedScore is '{$actualNormalizedScore}', is_numeric result: " . ($isActualNormalizedScoreNumeric ? 'true' : 'false'));

            $finalNormalizedScoreForDetails = $isActualNormalizedScoreNumeric ? round((float)$actualNormalizedScore, 4) : 'N/A_NORM_DETAIL_FALLBACK';

            $weight = $criterion['weight'] ?? 0;
            $contribution = 'N/A_CONTRIB_FALLBACK';
            if ($isActualNormalizedScoreNumeric && is_numeric($weight)) {
                $contribution = round((float)$actualNormalizedScore * (float)$weight, 4);
            }

            $detailsEntry = [
                'id' => $criterionId,
                'name' => $criterion['name'] ?? 'Unnamed Criterion',
                'type' => $criterion['type'] ?? 'N/A',
                'weight' => $weight,
                'rawValue' => $actualRawValue, // Storing the direct value or N/A_RAW_FALLBACK
                'displayValue' => $displayValue, // Storing the potentially mapped label or raw value
                'normalizedScore' => $finalNormalizedScoreForDetails,
                'contribution' => $contribution,
                'data_type' => $criterion['data_type'] ?? 'unknown',
                'options' => ($criterion['data_type'] === 'qualitative_option' && isset($criterion['options'])) ? $criterion['options'] : [],
            ];
            Log::debug("[PCD] For '{$criterionId}', final details entry for view: ", $detailsEntry);
            $this->criteriaDetails[] = $detailsEntry;
        }
        Log::debug("[PCD] Finished prepareCriteriaDetails. Final criteriaDetails for view: ", $this->criteriaDetails);
    }

    public function approveSubmission()
    {
        $this->updateStatus('approved', 'Submission approved successfully.');
    }

    public function rejectSubmission()
    {
        $this->updateStatus('rejected', 'Submission rejected successfully.');
    }

    public function openRevisionModal()
    {
        $this->showRevisionModal = true;
        $this->revisionNotes = '';
    }

    public function closeRevisionModal()
    {
        $this->showRevisionModal = false;
        $this->revisionNotes = '';
    }

    public function requestRevisionWithNotes()
    {
        $this->validate([
            'revisionNotes' => 'required|string|min:10|max:1000'
        ], [
            'revisionNotes.required' => 'Please provide revision notes for the teacher.',
            'revisionNotes.min' => 'Revision notes must be at least 10 characters.',
            'revisionNotes.max' => 'Revision notes cannot exceed 1000 characters.'
        ]);

        $this->updateStatus('need_revision', 'Submission marked as needing revision.', $this->revisionNotes);
        $this->closeRevisionModal();
    }

    public function requestRevision() // Legacy method for backward compatibility
    {
        $this->openRevisionModal();
    }

    private function updateStatus(string $status, string $message, ?string $notes = null) // Added notes parameter
    {
        $this->submission->update([
            'status' => $status,
            'revision_notes' => $notes,
            'status_updated_at' => now(),
            'status_updated_by' => Auth::id(),
        ]);
        session()->flash('message', $message);

        // Re-prepare details in case view depends on status or updated scores
        if ($this->batch->criteria_config && !empty($this->batch->criteria_config)) {
             try {
                // Correct: Call the main calculate method here as well
                $updatedSubmission = $this->sawCalculatorService->calculate($this->submission);
                $this->submission = $updatedSubmission;

                $this->sawScore = $this->submission->final_saw_score ?? null;
                $this->normalizedScores = $this->submission->normalized_scores ?? [];

                // Recapture calculation details after status update and recalculation
                if (isset($this->submission->calculation_details)) {
                    $this->sawCalculationSteps = $this->submission->calculation_details;
                    Log::debug("[ShowSubmission updateStatus] Recaptured SAW calculation_details: ", $this->sawCalculationSteps);
                } else {
                    $this->sawCalculationSteps = [];
                    Log::debug("[ShowSubmission updateStatus] SAW calculation_details not found on submission object after update.");
                }

                $this->prepareCriteriaDetails();
            } catch (\Exception $e) {
                Log::error("Error re-calculating SAW score after status update for submission ID {$this->submission->id}: " . $e->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.submissions.show-submission');
    }
}
