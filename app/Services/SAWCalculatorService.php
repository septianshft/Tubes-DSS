<?php

namespace App\Services;

use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\StudentSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SAWCalculatorService
{
    protected PredefinedCriteriaService $predefinedCriteriaService;
    private array $allCalculationSteps = [];

    public function __construct(PredefinedCriteriaService $predefinedCriteriaService)
    {
        $this->predefinedCriteriaService = $predefinedCriteriaService;
    }

    /**
     * Check if a value likely represents a file path
     */
    private function isFileValue($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $commonExtensions = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png', '.zip', '.rar'];
        foreach ($commonExtensions as $ext) {
            if (str_ends_with(strtolower($value), $ext)) {
                return true;
            }
        }

        if (str_starts_with($value, 'student_submissions/') || str_starts_with($value, 'uploads/')) {
            return true;
        }

        if ((str_contains($value, '/') || str_contains($value, '\\')) && !filter_var($value, FILTER_VALIDATE_URL)) {
            return true;
        }

        return false;
    }

    /**
     * Convert a value to numeric or return null if not directly convertible for calculation
     */
    private function convertToNumericOrOriginal($value): ?float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }

        if (is_string($value) && $this->isFileValue($value)) {
            return null;
        }

        if (is_string($value)) {
            return null;
        }

        if (is_bool($value) || $value === null) {
            return null;
        }

        return null;
    }

    /**
     * Get the mapped numeric value for a qualitative option
     */
    private function getMappedNumericValueForQualitative(string $criterionId, $rawValueFromStudent, array $criterionConfigFromBatch): ?float
    {
        if ($rawValueFromStudent === null || $rawValueFromStudent === '') {
            return null;
        }

        $optionsToSearch = $criterionConfigFromBatch['options'] ?? null;

        if (empty($optionsToSearch) || !is_array($optionsToSearch)) {
            $fullCriterionDefinition = $this->predefinedCriteriaService->getCriteriaDefinition($criterionId);
            if (!$fullCriterionDefinition || !isset($fullCriterionDefinition['options']) || !is_array($fullCriterionDefinition['options'])) {
                Log::warning("[SAWService->getMappedNumericValue] No options in full definition for criterion {$criterionId}. Cannot map value: {$rawValueFromStudent}");
                return null;
            }
            $optionsToSearch = $fullCriterionDefinition['options'];
        }

        foreach ($optionsToSearch as $option) {
            if (isset($option['value'], $option['numeric_value'])) {
                if ($option['value'] == $rawValueFromStudent) {
                    return (float)$option['numeric_value'];
                }
            }
        }

        return null;
    }

    /**
     * Retrieves the student's input value for a given predefined criterion.
     * Prioritizes values from the submission context, then falls back to student model attributes or defaults.
     *
     * @param Student $student The student model.
     * @param string $criterionId The ID of the criterion.
     * @param StudentSubmission|null $submissionContext The submission context, if available.
     * @return mixed The raw value for the criterion.
     */
    public function getStudentInputValueForPredefinedCriterion(
        Student $student,
        string $criterionId,
        ?StudentSubmission $submissionContext = null
    ): mixed {
        $logCtx = ['student_id' => $student->id, 'criterion_id' => $criterionId, 'has_submission_context' => (bool)$submissionContext];
        if ($submissionContext) {
            $logCtx['submission_id'] = $submissionContext->id;
            $logCtx['raw_values_in_context'] = $submissionContext->raw_criteria_values;
        }

        // Prioritize raw_criteria_values from the submission context if available
        if ($submissionContext && is_array($submissionContext->raw_criteria_values) && array_key_exists($criterionId, $submissionContext->raw_criteria_values)) {
            $valueFromContext = $submissionContext->raw_criteria_values[$criterionId];
            return $valueFromContext;
        }

        // Fallback to direct student model attributes or derived values
        switch ($criterionId) {
            case 'class_attendance_percentage':
                return $student->class_attendance_percentage ?? null;
            case 'average_score':
                return $student->average_score ?? null;
            case 'extracurricular_activeness':
                return $student->extracurricular_score ?? null;

            // For criteria that are purely submission-based or complex derivations not on student model:
            case 'major_relevance':
            case 'graduation_time_gap':
                return null;

            // Qualitative criteria with fallbacks from student model (if applicable)
            case 'tuition_payment_delays':
            case 'disciplinary_warnings':
                return null;

            default:
                if (property_exists($student, $criterionId)) {
                    return $student->{$criterionId};
                }
                return null;
        }
    }

    /**
     * Calculate SAW scores for a collection of student submissions for a given batch.
     *
     * @param ScholarshipBatch $batch
     * @param Collection $submissions
     * @return Collection The submissions collection with 'final_saw_score' and 'normalized_scores' appended to each.
     */
    public function calculateScoresForBatch(ScholarshipBatch $batch, Collection $submissionsInBatch, ?StudentSubmission $detailPageSubmissionContext = null)
    {
        $detailPageSubmissionId = $detailPageSubmissionContext ? $detailPageSubmissionContext->id : 'None';
        Log::debug("[SAWCalculatorService->calculateScoresForBatch] Called. Batch ID: {$batch->id}. Submissions: " . $submissionsInBatch->count() . ". Detail Context Submission ID: {$detailPageSubmissionId}");

        $batchCriteriaConfig = $batch->criteria_config;

        if (empty($batchCriteriaConfig) || !is_array($batchCriteriaConfig)) {
            Log::error("[SAWCalculatorService->calculateScoresForBatch] Batch criteria configuration is empty or invalid for Batch ID: {$batch->id}. Cannot calculate scores.");
            foreach ($submissionsInBatch as $submission) {
                $submission->normalized_scores = [];
                $submission->final_saw_score = 0;
                $submission->saveQuietly();
            }
            if ($detailPageSubmissionContext) {
                 $detailPageSubmissionContext->normalized_scores = [];
                 $detailPageSubmissionContext->final_saw_score = 0;
                 $this->allCalculationSteps[$detailPageSubmissionContext->id] = ['error' => 'Batch criteria configuration is missing or invalid.', 'steps' => [], 'summary' => []];
                 $submissionsInBatch = $submissionsInBatch->map(function($sub) use ($detailPageSubmissionContext) {
                    return $sub->id === $detailPageSubmissionContext->id ? $detailPageSubmissionContext : $sub;
                 });
            }
            return $submissionsInBatch;
        }

        // Initialize calculation steps array for the detail page context if provided
        if ($detailPageSubmissionContext) {
            $this->allCalculationSteps[$detailPageSubmissionContext->id] = ['steps' => [], 'summary' => []];
        }

        $cohortMinMaxValues = [];
        $numericValuesForCriterionAcrossCohort = [];

        // Calculate Min/Max for each criterion based on the cohort (all submissions in the batch)
        foreach ($batchCriteriaConfig as $critConfig) {
            if (!isset($critConfig['id']) || !isset($critConfig['data_type'])) {
                Log::warning("[SAWCalculatorService->calculateScoresForBatch] Criterion config missing 'id' or 'data_type'", ['config' => $critConfig]);
                continue;
            }
            $criterionId = $critConfig['id'];
            $numericValuesForCriterionAcrossCohort[$criterionId] = [];

            foreach ($submissionsInBatch as $sub) {
                $rawValue = $this->getStudentInputValueForPredefinedCriterion($sub->student, $criterionId, $sub);

                $valueForMinMax = null;
                if ($critConfig['data_type'] === 'qualitative_option') {
                    $valueForMinMax = $this->getMappedNumericValueForQualitative($criterionId, $rawValue, $critConfig);
                } elseif ($critConfig['data_type'] === 'numeric' || $critConfig['data_type'] === 'file') {
                    $converted = $this->convertToNumericOrOriginal($rawValue);
                    if (is_numeric($converted)) {
                        $valueForMinMax = (float)$converted;
                    }
                }

                if ($valueForMinMax !== null) {
                    $numericValuesForCriterionAcrossCohort[$criterionId][] = $valueForMinMax;
                }
            }

            if (!empty($numericValuesForCriterionAcrossCohort[$criterionId])) {
                $cohortMinMaxValues[$criterionId] = [
                    'min' => min($numericValuesForCriterionAcrossCohort[$criterionId]),
                    'max' => max($numericValuesForCriterionAcrossCohort[$criterionId]),
                ];
            } else {
                $criterionDisplayName = $critConfig['name'] ?? $criterionId;
                Log::warning("[SAWService MinMax] No numeric values for criterion '{$criterionDisplayName}' (ID: {$criterionId}) in Batch ID {$batch->id}. Setting min/max to 0/0.");
                $cohortMinMaxValues[$criterionId] = ['min' => 0.0, 'max' => 0.0];
            }
        }
        Log::debug("[SAWCalculatorService->calculateScoresForBatch] CohortMinMaxValues (Batch ID {$batch->id}): ", $cohortMinMaxValues);

        // Normalize scores for each submission
        $processedSubmissions = new Collection();
        foreach ($submissionsInBatch as $submission) {
            // Check if the current submission is the one we're generating detailed steps for
            $currentSubmissionIsDetailPageContext = $detailPageSubmissionContext && $submission->id === $detailPageSubmissionContext->id;

            // Initialize/reset steps for this submission if it's the detail context
            if ($currentSubmissionIsDetailPageContext) {
                 $this->allCalculationSteps[$submission->id] = ['steps' => [], 'summary' => []];
            }

            $normalizedScores = [];
            $totalWeightedScore = 0;
            $submissionCalculationSteps = [];

            foreach ($batchCriteriaConfig as $critConfig) {
                if (!isset($critConfig['id'])) continue;

                $criterionId = $critConfig['id'];
                $criterionName = $critConfig['name'] ?? $criterionId;
                $criterionType = $critConfig['type'] ?? 'benefit';
                $criterionWeight = (float)($critConfig['weight'] ?? 0);
                $criterionDataType = $critConfig['data_type'] ?? 'unknown';

                $stepDetail = [
                    'criterion_id' => $criterionId,
                    'criterion_name' => $criterionName,
                    'criterion_type' => $criterionType,
                    'criterion_weight' => $criterionWeight,
                    'data_type' => $criterionDataType,
                ];

                $rawValueFromStudent = $this->getStudentInputValueForPredefinedCriterion($submission->student, $criterionId, $submission);
                $stepDetail['raw_value_submitted'] = $rawValueFromStudent;

                $numericValueForCalc = null;
                if ($criterionDataType === 'qualitative_option') {
                    $numericValueForCalc = $this->getMappedNumericValueForQualitative($criterionId, $rawValueFromStudent, $critConfig);
                } elseif ($criterionDataType === 'numeric' || $criterionDataType === 'file') {
                    $converted = $this->convertToNumericOrOriginal($rawValueFromStudent);
                    if (is_numeric($converted)) {
                        $numericValueForCalc = (float)$converted;
                    }
                }
                $stepDetail['numeric_value_for_calc'] = $numericValueForCalc;

                // Fetch min/max for this criterion from the pre-calculated cohort values
                $minVal = $cohortMinMaxValues[$criterionId]['min'] ?? 0.0;
                $maxVal = $cohortMinMaxValues[$criterionId]['max'] ?? 0.0;
                $stepDetail['min_value_for_criterion'] = $minVal;
                $stepDetail['max_value_for_criterion'] = $maxVal;

                $normalizedValue = 0.0;
                $normalizationFormulaString = "N/A";

                if ($numericValueForCalc !== null) {
                    if ($criterionType === 'benefit') {
                        if (($maxVal - $minVal) == 0) {
                            $normalizedValue = ($maxVal == 0 && $minVal == 0 && $numericValueForCalc == 0) ? 0.0 : 1.0;
                            $normalizationFormulaString = "Benefit (max-min=0): " . ($normalizedValue == 1.0 ? "value equals max/min" : "all values zero or single value");
                        } else {
                            $normalizedValue = ($numericValueForCalc - $minVal) / ($maxVal - $minVal);
                            $normalizationFormulaString = "Benefit ({$criterionName}): ({$numericValueForCalc} - {$minVal}) / ({$maxVal} - {$minVal})";
                        }
                    } elseif ($criterionType === 'cost') {
                        if (($maxVal - $minVal) == 0) {
                            $normalizedValue = ($maxVal == 0 && $minVal == 0 && $numericValueForCalc == 0) ? 0.0 : 1.0;
                            $normalizationFormulaString = "Cost (max-min=0): " . ($normalizedValue == 1.0 ? "value equals max/min" : "all values zero or single value");
                        } else {
                            $normalizedValue = ($maxVal - $numericValueForCalc) / ($maxVal - $minVal);
                            $normalizationFormulaString = "Cost ({$criterionName}): ({$maxVal} - {$numericValueForCalc}) / ({$maxVal} - {$minVal})";
                        }
                    }
                } else {
                    $normalizationFormulaString = "N/A (value '{$rawValueFromStudent}' could not be converted to numeric for {$criterionName})";
                    $stepDetail['error'] = "Raw value '{$rawValueFromStudent}' could not be converted to a numeric score for criterion '{$criterionName}'. Normalized to 0.";
                }

                $stepDetail['normalization_formula_string'] = $normalizationFormulaString;
                $stepDetail['normalized_value_before_clamping'] = $normalizedValue;

                // Ensure finite and clamp between 0 and 1
                $normalizedValue = is_finite($normalizedValue) ? $normalizedValue : 0.0;
                $normalizedValue = max(0.0, min(1.0, $normalizedValue));
                $stepDetail['normalized_value_after_clamping'] = $normalizedValue;

                $normalizedScores[$criterionId] = round($normalizedValue, 4);
                $stepDetail['normalized_value_stored'] = $normalizedScores[$criterionId];

                $weightedContribution = $normalizedScores[$criterionId] * $criterionWeight;
                $stepDetail['weighted_score_contribution'] = $weightedContribution;
                $totalWeightedScore += $weightedContribution;

                // If this is the submission for which detailed steps are requested, store them.
                if ($currentSubmissionIsDetailPageContext) {
                    $submissionCalculationSteps[] = $stepDetail;
                }
            }

            $submission->normalized_scores = $normalizedScores;

            // Store final SAW score without tie-breaking to allow true academic ties
            $submission->final_saw_score = round($totalWeightedScore, 4);

            // Store detailed steps and summary if this is the context submission
            if ($currentSubmissionIsDetailPageContext) {
                $this->allCalculationSteps[$submission->id]['steps'] = $submissionCalculationSteps;
                $this->allCalculationSteps[$submission->id]['summary'] = [
                    'total_weighted_score_from_steps' => $totalWeightedScore,
                    'final_saw_score_rounded' => $submission->final_saw_score,
                    'note' => "Academic scoring: Students with identical scores receive the same rank.",
                ];
                Log::debug("[SAWCalculatorService->calculateScoresForBatch] (Detail Log) Submission ID: {$submission->id}. Final Score: {$submission->final_saw_score}. Details: ", $this->allCalculationSteps[$submission->id]);
            }

            $processedSubmissions->push($submission);
        }

                // Rank submissions
        $rankedSubmissions = $this->rankSubmissions($processedSubmissions);

        // Save all processed submissions (scores to database)
        foreach ($rankedSubmissions as $sub) {
            DB::table('student_submissions')
                ->where('id', $sub->id)
                ->update([
                    'final_saw_score' => $sub->final_saw_score,
                    'normalized_scores' => json_encode($sub->normalized_scores),
                    'updated_at' => now(),
                ]);
        }

        // If a detail page context was provided, store its calculation details in session for retrieval by the controller/Livewire component
        if ($detailPageSubmissionContext && isset($this->allCalculationSteps[$detailPageSubmissionContext->id])) {
            session(['saw_calculation_details_for_submission_' . $detailPageSubmissionContext->id => $this->allCalculationSteps[$detailPageSubmissionContext->id]]);
        }

        // Clear the session flag that indicated a detail page calculation was in progress
        if (session()->has('saw_detail_page_submission_id')) {
            session()->forget('saw_detail_page_submission_id');
        }

        Log::debug("[SAWCalculatorService->calculateScoresForBatch] Finished. Processed and ranked " . $rankedSubmissions->count() . " submissions for Batch ID: {$batch->id}");
        return $rankedSubmissions;
    }

    /**
     * Calculates the SAW score for a single student submission within the context of its batch.
     * This method orchestrates the calculation by calling calculateScoresForBatch,
     * ensuring that cohort-based normalization (min/max) is done correctly.
     *
     * @param StudentSubmission $submission The submission to calculate the score for.
     * @return StudentSubmission The updated submission with scores and rank.
     */
    public function calculateScore(StudentSubmission $submission): StudentSubmission
    {
        Log::debug("[SAWCalculatorService->calculate] Called for Submission ID: {$submission->id}");
        $batch = $submission->scholarshipBatch; // Assumes relation is loaded or loads it

        if (!$batch) {
            Log::error("[SAWCalculatorService->calculate] ScholarshipBatch not found for Submission ID: {$submission->id}. Cannot calculate score.");
            $submission->normalized_scores = [];
            $submission->final_saw_score = 0;
            return $submission;
        }

        // Store the ID of the submission for which we want detailed calculation steps.
        session(['saw_detail_page_submission_id' => $submission->id]);

        // Get all submissions for the batch, ensuring student relation is loaded for getStudentInputValueForPredefinedCriterion
        $allSubmissionsInBatch = StudentSubmission::where('scholarship_batch_id', $batch->id)
                                                ->with('student')
                                                ->get();

        Log::debug("[SAWCalculatorService->calculate] Fetched " . $allSubmissionsInBatch->count() . " total submissions for Batch ID: {$batch->id} to calculate for Submission ID: {$submission->id}");

        // The calculateScoresForBatch method will handle updating all submissions in the batch
        $updatedSubmissions = $this->calculateScoresForBatch($batch, $allSubmissionsInBatch, $submission);

        // Find and return the updated version of the originally passed submission from the processed collection
        $finalUpdatedSubmission = $updatedSubmissions->firstWhere('id', $submission->id);

        if (!$finalUpdatedSubmission) {
            Log::error("[SAWCalculatorService->calculate] The target submission ID {$submission->id} was not found in the processed batch results. This should not happen. Returning original submission.");
            return $submission->fresh();
        }

        Log::debug("[SAWCalculatorService->calculate] Returning processed Submission ID: {$finalUpdatedSubmission->id} with Final Score: {$finalUpdatedSubmission->final_saw_score}. Normalized: ", (array)$finalUpdatedSubmission->normalized_scores);
        return $finalUpdatedSubmission;
    }

    /**
     * Ranks a collection of submissions based on their final_saw_score.
     * Higher scores get better ranks. Properly handles ties by giving identical ranks to identical scores.
     */
    protected function rankSubmissions(Collection $submissions): Collection
    {
        $ranked = $submissions->sortByDesc('final_saw_score')
                             ->sortBy('id') // Secondary sort for consistent ordering
                             ->values();

        $currentRank = 1;
        $previousScore = null;
        $studentsAtCurrentRank = 0;

        $submissionsWithRank = $ranked->map(function ($submission) use (&$currentRank, &$previousScore, &$studentsAtCurrentRank) {
            if ($previousScore !== null && $submission->final_saw_score < $previousScore) {
                // Score is lower than previous, advance rank by number of students who had the previous score
                $currentRank += $studentsAtCurrentRank;
                $studentsAtCurrentRank = 1;
            } else {
                // Same score as previous or first student
                $studentsAtCurrentRank++;
            }

            $submission->rank = $currentRank;
            $previousScore = $submission->final_saw_score;

            return $submission;
        });

        return $submissionsWithRank;
    }

    /**
     * Retrieves the detailed calculation steps for a specific submission, if available.
     * These steps are typically generated when calculateScoresForBatch is called with a $detailPageSubmissionContext.
     */
    public function getCalculationStepsForSubmission(int $submissionId): ?array
    {
        // Check session first, as it might be stored there from a recent calculation for a detail page.
        $sessionKey = 'saw_calculation_details_for_submission_' . $submissionId;
        if (session()->has($sessionKey)) {
            $details = session($sessionKey);
            return $details;
        }

        // Fallback to the internal property if session doesn't have it.
        if (isset($this->allCalculationSteps[$submissionId])) {
            return $this->allCalculationSteps[$submissionId];
        }

        return null;
    }
}
