<?php

namespace App\Services;

use App\Models\Student; // Added
use App\Models\StudentSubmission;
use App\Models\ScholarshipBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SAWCalculatorService
{
    /**
     * Calculate the SAW score for a single student for a given scholarship batch.
     *
     * @param Student $student
     * @param ScholarshipBatch $batch
     * @return float The calculated SAW score. Returns 0 if criteria are not configured.
     */
    public function calculateScore(Student $student, ScholarshipBatch $batch): float
    {
        $criteriaConfig = $batch->criteria_config ?? [];

        if (empty($criteriaConfig)) {
            Log::warning("[SAWCalculatorService->calculateScore] No criteria configured for Batch ID: {$batch->id}. Returning score 0 for Student ID: {$student->id}");
            return 0.0;
        }

        $totalWeightedScore = 0.0;
        $normalizationValues = $this->prepareNormalizationValues($batch, $criteriaConfig);

        foreach ($criteriaConfig as $criterion) {
            if (!isset($criterion['student_attribute'])) {
                Log::warning("[SAWCalculatorService->calculateScore] Missing 'student_attribute' in criterion for Batch ID: {$batch->id}. Skipping criterion '{$criterion['name']}'.");
                continue;
            }

            // Check if the student attribute is set and not null
            if (!isset($student->{$criterion['student_attribute']})) {
                Log::warning("[SAWCalculatorService->calculateScore] Student attribute '{$criterion['student_attribute']}' is null for Student ID: {$student->id} in Batch ID: {$batch->id}. Skipping criterion '{$criterion['name']}'.");
                continue;
            }

            $rawValue = (float) $student->{$criterion['student_attribute']};
            $weight = (float) ($criterion['weight'] ?? 0);
            $type = $criterion['type'] ?? 'benefit'; // 'benefit' or 'cost'
            $criterionId = $criterion['id']; // Used for fetching min/max

            if ($weight === 0.0) {
                Log::info("[SAWCalculatorService->calculateScore] Criterion '{$criterion['name']}' has zero weight for Batch ID: {$batch->id}. Skipping.");
                continue;
            }

            $min = $normalizationValues[$criterionId]['min'] ?? null;
            $max = $normalizationValues[$criterionId]['max'] ?? null;

            if ($min === null || $max === null) {
                 Log::warning("[SAWCalculatorService->calculateScore] Min/Max not found for criterion '{$criterion['name']}' (ID: {$criterionId}) in Batch ID: {$batch->id}. This might happen if no students have this criterion or submitted relevant data. Skipping normalization for this criterion.");
                 continue;
            }

            $normalizedValue = 0.0;

            if ($type === 'benefit') {
                if ($max == $min) {
                    $normalizedValue = ($max > 0) ? 1 : 0;
                } elseif ($max > $min) {
                    $normalizedValue = ($rawValue - $min) / ($max - $min);
                }
            } elseif ($type === 'cost') {
                if ($max == $min) {
                     $normalizedValue = ($min > 0) ? 1 : 0;
                } elseif ($max > $min) {
                     $normalizedValue = ($max - $rawValue) / ($max - $min);
                }
            }

            $normalizedValue = max(0, min(1, $normalizedValue));

            $totalWeightedScore += $normalizedValue * $weight;
        }

        Log::info("[SAWCalculatorService->calculateScore] Calculated SAW score {$totalWeightedScore} for Student ID: {$student->id}, Batch ID: {$batch->id}");
        return round($totalWeightedScore, 4); // Round to 4 decimal places
    }

    /**
     * Prepare min/max values for normalization across all students for a given batch.
     *
     * @param ScholarshipBatch $batch
     * @param array $criteriaConfig
     * @return array
     */
    private function prepareNormalizationValues(ScholarshipBatch $batch, array $criteriaConfig): array
    {
        $normalizationValues = [];

        $submittedStudents = Student::whereHas('submissions', function ($query) use ($batch) {
            $query->where('scholarship_batch_id', $batch->id);
        })->get();

        if ($submittedStudents->isEmpty()) {
            Log::warning("[SAWCalculatorService->prepareNormalizationValues] No students found with submissions for normalization in Batch ID: {$batch->id}. Min/Max values will be null.");
            foreach ($criteriaConfig as $criterion) {
                if (!isset($criterion['id'])) continue;
                $normalizationValues[$criterion['id']] = ['min' => null, 'max' => null];
            }
            return $normalizationValues;
        }

        foreach ($criteriaConfig as $criterion) {
            if (!isset($criterion['student_attribute']) || !isset($criterion['id'])) {
                continue;
            }
            $attribute = $criterion['student_attribute'];
            $criterionId = $criterion['id'];

            $values = $submittedStudents->map(function ($student) use ($attribute) {
                return $student->{$attribute};
            })->filter(function ($value) {
                return is_numeric($value);
            })->map(function ($value) {
                return (float) $value;
            });

            if ($values->isNotEmpty()) {
                $normalizationValues[$criterionId] = [
                    'min' => $values->min(),
                    'max' => $values->max(),
                ];
            } else {
                $normalizationValues[$criterionId] = ['min' => null, 'max' => null];
                 Log::info("[SAWCalculatorService->prepareNormalizationValues] No numeric values found for attribute '{$attribute}' among submitted students for Batch ID: {$batch->id}. Criterion ID: {$criterionId}");
            }
        }
        return $normalizationValues;
    }

    /**
     * Calculate SAW scores for a collection of student submissions for a given batch.
     *
     * @param ScholarshipBatch $batch
     * @param Collection $submissions
     * @return Collection The submissions collection with 'final_saw_score' and 'normalized_scores' appended to each.
     */
    public function calculateScoresForBatch(ScholarshipBatch $batch, Collection $submissions): Collection
    {
        // Retrieve criteria configuration for the batch, default to empty array if not set.
        $criteriaConfig = $batch->criteria_config ?? [];
        Log::debug("[SAWCalculatorService->calculateScoresForBatch] Called. Batch ID: {$batch->id}. Received submissions count: " . $submissions->count());
        // Debug log if only one submission is present.
        if ($submissions->count() < 2 && $submissions->isNotEmpty()) {
            Log::debug("[SAWCalculatorService->calculateScoresForBatch] Only one submission in the collection. Submission ID: " . $submissions->first()->id);
        }

        // If no criteria are configured or no submissions exist,
        // set final scores to 0 and normalized scores to an empty array for all submissions.
        if (empty($criteriaConfig) || $submissions->isEmpty()) {
            $submissions->each(function ($sub) {
                $sub->final_saw_score = 0; // Set final score to 0.
                $sub->normalized_scores = []; // Set normalized scores to an empty array.
            });
            return $submissions; // Return submissions as is.
        }

        // Initialize arrays to store min/max values and all criterion values.
        $minMaxValues = []; // Stores min and max values for each criterion.
        $allCriterionValues = []; // Aggregates all student values for each criterion.

        // Initialize $allCriterionValues for each criterion ID.
        // This ensures a container exists for each criterion's values.
        foreach ($criteriaConfig as $criterion) {
            if (!isset($criterion['id'])) {
                Log::warning("SAWCalculatorService: Criterion missing 'id' in batch {$batch->id}", ['criterion' => $criterion]);
                continue; // Skip to the next criterion if 'id' is missing.
            }
            $criterionId = $criterion['id'];
            $allCriterionValues[$criterionId] = []; // Create an empty array for this criterion ID.
        }

        // Pass 1: Collect all raw (numeric) values for each criterion from all submissions.
        // This pass iterates through each submission.
        foreach ($submissions as $submission) {
            // Retrieve raw criteria values submitted by the student (typically JSON).
            $rawValues = $submission->raw_criteria_values ?? [];
            // Inner loop: Iterate through each criterion in the batch configuration.
            foreach ($criteriaConfig as $criterion) {
                if (!isset($criterion['id'])) continue; // Skip if criterion 'id' is missing.
                $criterionId = $criterion['id'];

                $currentValue = null; // Student's value for this criterion, initially null.
                // Check if the student submitted a value for this criterion.
                if (isset($rawValues[$criterionId])) {
                    // If 'value_map' exists, it's a qualitative criterion (e.g., "Good", "Fair").
                    // Convert the textual/option value to its predefined numeric score.
                    if (isset($criterion['value_map']) && is_array($criterion['value_map'])) { // Qualitative
                        $currentValue = $criterion['value_map'][$rawValues[$criterionId]] ?? null;
                    } else { // Numeric
                        // Otherwise, assume numeric. Ensure the value is a number.
                        $currentValue = is_numeric($rawValues[$criterionId]) ? (float)$rawValues[$criterionId] : null;
                    }
                }

                // If a valid numeric value was processed for the student for this criterion,
                // add it to the collection of values for this criterion.
                if ($currentValue !== null) {
                    // Ensure the criterion ID exists as a key in $allCriterionValues before appending.
                    if (array_key_exists($criterionId, $allCriterionValues)) {
                        $allCriterionValues[$criterionId][] = $currentValue;
                    }
                }
            }
        }

        // After collecting all values, determine the actual min/max for each criterion.
        // This is crucial for the subsequent normalization process.
        foreach ($criteriaConfig as $criterion) {
            if (!isset($criterion['id'])) continue; // Skip if criterion 'id' is missing.
            $criterionId = $criterion['id'];
            // Check if any values were collected for this criterion.
            if (array_key_exists($criterionId, $allCriterionValues) && !empty($allCriterionValues[$criterionId])) {
                // If values exist, find their minimum and maximum.
                $minMaxValues[$criterionId] = [
                    'min' => min($allCriterionValues[$criterionId]),
                    'max' => max($allCriterionValues[$criterionId]),
                ];
            } else {
                // If no values were found (e.g., all submissions had null for this criterion),
                // default min/max to 0 to prevent errors.
                $minMaxValues[$criterionId] = ['min' => 0, 'max' => 0];
            }
        }
        // Log the calculated min/max values for debugging purposes.
        Log::debug("[SAWCalculatorService->calculateScoresForBatch] Calculated MinMaxValues for Batch ID {$batch->id}: ", $minMaxValues);

        // Pass 2: Normalize and calculate final scores for each submission
        foreach ($submissions as $idx => $submission) {
            // Ambil nilai mentah yang dikirim siswa untuk pengajuan ini.
            $rawValues = $submission->raw_criteria_values ?? [];
            $submissionNormalizedScores = [];
            $finalScore = 0;
            $currentSubmissionCalculationSteps = []; // Initialize for the current submission's steps

            // --- Opsional: Logging detail untuk pengajuan tertentu jika ditandai dari halaman detail ---
            $logForThisSubmission = false;
            $detailPageSubmissionId = session('saw_detail_page_submission_id');
            if ($detailPageSubmissionId && $detailPageSubmissionId == $submission->id) {
                $logForThisSubmission = true;
                Log::debug("[SAWCalculatorService->calculateScoresForBatch] Processing for (detail page) Submission ID: {$submission->id} within Batch ID {$batch->id}. Raw values: ", $rawValues);
            }


            // Loop dalam: Iterasi melalui setiap kriteria yang ditentukan dalam konfigurasi batch beasiswa.
            foreach ($criteriaConfig as $criterion) {
                // Validasi kriteria ID bobot dan tipe
                if (!isset($criterion['id']) || !isset($criterion['weight']) || !isset($criterion['type'])) {
                     Log::warning("SAWCalculatorService: Kriteria kehilangan 'id', 'weight', atau 'type' di batch {$batch->id}", ['criterion' => $criterion]);

                     $criterionIdForError = $criterion['id'] ?? null; // Get ID if available

                    if ($logForThisSubmission) {
                        $currentSubmissionCalculationSteps[] = [
                            'criterion_id' => $criterionIdForError ?? 'N/A_CFG_ERR',
                            'criterion_name' => $criterion['name'] ?? ($criterionIdForError ?? 'N/A_CFG_ERR'),
                            'error' => "Criterion configuration missing id, weight, or type. Normalized to 0.",
                            'normalized_value_from_formula' => 0, // Added for consistency
                            'normalized_value_stored' => 0,       // Added for consistency
                            'weighted_score_contribution' => 0,   // Added for consistency
                        ];
                    }

                    if ($criterionIdForError) { // If we have an ID
                        $submissionNormalizedScores[$criterionIdForError] = 0; // Ensure a default is set
                    }
                    continue; // Then skip further processing for this malformed criterion
                }
                $criterionId = $criterion['id']; // This is safe now
                $criterionName = $criterion['name'] ?? $criterionId; // Use name if available, else id
                $criterionType = $criterion['type'];
                $criterionWeight = (float)($criterion['weight'] ?? 0);

                // Ambil nilai siswa untuk kriteria.
                $studentRawValue = $rawValues[$criterionId] ?? null;
                $currentValue = null;
                if (isset($rawValues[$criterionId])) {
                     // Kalau kualitatif, ambil dari value_map.
                     if (isset($criterion['value_map']) && is_array($criterion['value_map'])) {
                        $currentValue = $criterion['value_map'][$rawValues[$criterionId]] ?? null;
                    } else {
                        $currentValue = is_numeric($rawValues[$criterionId]) ? (float)$rawValues[$criterionId] : null;
                    }
                }

                if ($currentValue === null) {
                    $submissionNormalizedScores[$criterionId] = 0; // Default normalized score for missing value
                    if ($logForThisSubmission) {
                        $currentSubmissionCalculationSteps[] = [
                            'criterion_id' => $criterionId,
                            'criterion_name' => $criterionName,
                            'raw_value_submitted' => $studentRawValue,
                            'numeric_value' => null,
                            'min_value_for_criterion' => $minMaxValues[$criterionId]['min'] ?? 'N/A',
                            'max_value_for_criterion' => $minMaxValues[$criterionId]['max'] ?? 'N/A',
                            'criterion_type' => $criterionType,
                            'criterion_weight' => $criterionWeight,
                            'normalization_formula_string' => 'Student did not provide a value, or value was invalid. Normalized to 0.',
                            'normalized_value_from_formula' => 0, // Consistent with other calculation steps
                            'normalized_value_stored' => 0,
                            'weighted_score_contribution' => 0,
                        ];
                    }
                    // No score added to $finalScore for this criterion if value is null
                    continue;
                }

                // Validasi nilai min max
                if (!isset($minMaxValues[$criterionId])) {
                    Log::error("SAWCalculatorService: Nilai min/max tidak ditemukan untuk ID kriteria {$criterionId} di batch {$batch->id}");
                    $submissionNormalizedScores[$criterionId] = 0;
                     if ($logForThisSubmission) {
                        $currentSubmissionCalculationSteps[] = [
                            'criterion_id' => $criterionId,
                            'criterion_name' => $criterionName,
                            'raw_value_submitted' => $studentRawValue,
                            'numeric_value' => $currentValue,
                            'error' => "Min/max values not found for criterion. Normalized to 0.",
                            'normalized_value_from_formula' => 0, // Added for consistency
                            'normalized_value_stored' => 0,
                            'weighted_score_contribution' => 0,
                        ];
                    }
                    continue;
                }


                $minValue = $minMaxValues[$criterionId]['min'];
                $maxValue = $minMaxValues[$criterionId]['max'];
                $normalizedValue = 0; // This will hold the value after formula
                $normalizationFormulaString = '';


                if ($maxValue == $minValue) {
                    $normalizedValue = ($maxValue == 0 && $minValue == 0) ? 0 : 1;
                    $normalizationFormulaString = "Max ({$maxValue}) == Min ({$minValue}). Initial normalized value: {$normalizedValue}.";
                } else {
                    if ($criterionType === 'benefit') {
                        $normalizedValue = ($currentValue - $minValue) / ($maxValue - $minValue);
                        $normalizationFormulaString = "Benefit: ({$currentValue} - {$minValue}) / ({$maxValue} - {$minValue}) = {$normalizedValue}";
                    } elseif ($criterionType === 'cost') {
                        $normalizedValue = ($maxValue - $currentValue) / ($maxValue - $minValue);
                        $normalizationFormulaString = "Cost: ({$maxValue} - {$currentValue}) / ({$maxValue} - {$minValue}) = {$normalizedValue}";
                    }
                }

                // Clamping to [0,1] is removed as per user request for raw DSS score.
                // The $normalizedValue (result of benefit/cost formula) is used directly.
                // The $normalizationFormulaString already reflects the direct formula result.

                $roundedNormalizedValueForStorage = round($normalizedValue, 4);
                $weightedScoreComponent = $normalizedValue * $criterionWeight;

                $submissionNormalizedScores[$criterionId] = $roundedNormalizedValueForStorage;
                $finalScore += $weightedScoreComponent;

                if ($logForThisSubmission) {
                    $currentSubmissionCalculationSteps[] = [
                        'criterion_id' => $criterionId,
                        'criterion_name' => $criterionName,
                        'raw_value_submitted' => $studentRawValue,
                        'numeric_value' => $currentValue,
                        'min_value_for_criterion' => $minValue,
                        'max_value_for_criterion' => $maxValue,
                        'criterion_type' => $criterionType,
                        'criterion_weight' => $criterionWeight,
                        'normalization_formula_string' => $normalizationFormulaString,
                        'normalized_value_from_formula' => $normalizedValue, // Raw value from normalization formula
                        'normalized_value_stored' => $roundedNormalizedValueForStorage, // Rounded version of the above
                        'weighted_score_contribution' => $weightedScoreComponent,
                    ];
                }
            } // End of criteria loop

            // Assign the collected normalized scores to the submission object
            $submission->normalized_scores = $submissionNormalizedScores;

            $scoreBeforeTieBreaking = $finalScore;
            $tieBreakingFactor = 0.0;

            // Apply a very small, deterministic tie-breaker based on submission ID
            // This subtracts a tiny amount for higher IDs, making earlier submissions (lower IDs) slightly favored in ties.
            // The 1.0e-9 factor ensures the adjustment is very small.
            if (isset($submission->id) && is_numeric($submission->id)) {
                $tieBreakingFactor = -((float)$submission->id * 1.0e-9); // Ensure float arithmetic
                $finalScore += $tieBreakingFactor;
            }
            $scoreAfterTieBreakingAndBeforeRounding = $finalScore;

            $submission->final_saw_score = round($finalScore, 4);

            // Populate calculation_details if this is the submission being detailed
            if ($logForThisSubmission) {
                $submission->calculation_details = [
                    'steps' => $currentSubmissionCalculationSteps, // existing steps for criteria
                    'summary' => [
                        'sum_weighted_scores_from_criteria' => round($scoreBeforeTieBreaking, 8),
                        'tie_breaking_adjustment_applied' => $tieBreakingFactor,
                        'score_after_tie_breaking_before_rounding' => round($scoreAfterTieBreakingAndBeforeRounding, 8),
                        'final_saw_score_rounded' => $submission->final_saw_score,
                        'tie_breaking_note' => 'A small adjustment (-ID * 1.0e-9) was applied to the sum of weighted scores before final rounding. This aims to differentiate scores that would otherwise be identical, slightly favoring submissions with lower IDs (typically earlier ones).'
                    ]
                ];
                Log::debug("[SAWCalculatorService->calculateScoresForBatch] (Detail Log) Submission ID: {$submission->id}. Score before tie-break: " . round($scoreBeforeTieBreaking, 8) . ", Tie-break factor: {$tieBreakingFactor}, Score after tie-break (before round): " . round($scoreAfterTieBreakingAndBeforeRounding, 8) . ", Final rounded score: {$submission->final_saw_score}");
            }
        } // End of submissions loop

        if ($detailPageSubmissionId) {
            session()->forget('saw_detail_page_submission_id');
        }

        return $submissions;
    }

    /**
     * Calculate the SAW score for a single student submission.
     *
     * @param StudentSubmission $submission
     * @return StudentSubmission
     */
    public function calculate(StudentSubmission $submission): StudentSubmission
    {
        Log::debug("[SAWCalculatorService->calculate] Called for Submission ID: {$submission->id}");
        session(['saw_detail_page_submission_id' => $submission->id]); // Flag for more detailed logging

        $batch = $submission->scholarshipBatch;
        if (!$batch) {
            $submission->final_saw_score = 0;
            $submission->normalized_scores = [];
            Log::warning("[SAWCalculatorService] Attempted to calculate score for submission ID {$submission->id} which has no associated batch.");
            return $submission;
        }

        // Fetch all submissions for the batch to ensure correct normalization context
        $allSubmissionsForBatch = StudentSubmission::where('scholarship_batch_id', $batch->id)->get();
        Log::debug("[SAWCalculatorService->calculate] Fetched " . $allSubmissionsForBatch->count() . " total submissions for Batch ID: {$batch->id} to calculate for Submission ID: {$submission->id}");
        // ADDED: Log the IDs of all fetched submissions
        Log::debug("[SAWCalculatorService->calculate] IDs of fetched submissions: ", $allSubmissionsForBatch->pluck('id')->toArray());

        if ($allSubmissionsForBatch->isEmpty()) {
            // Should not happen if $submission exists and belongs to this batch, but as a safeguard
            $submission->final_saw_score = 0;
            $submission->normalized_scores = [];
            Log::warning("[SAWCalculatorService] No submissions found for batch ID {$batch->id} when calculating for submission ID {$submission->id}.");
            return $submission;
        }

        // Calculate scores for all submissions in the batch
        $processedSubmissions = $this->calculateScoresForBatch($batch, $allSubmissionsForBatch);

        // Find and return the original submission from the processed collection
        $resultSubmission = $processedSubmissions->firstWhere('id', $submission->id);

        if (!$resultSubmission) {
            // Fallback, should ideally not be reached if logic is correct
            Log::error("[SAWCalculatorService->calculate] Original submission ID {$submission->id} not found in processed collection for batch ID {$batch->id}. Returning original with potentially incorrect scores.");
            // session()->forget('saw_detail_page_submission_id'); // Already cleared in calculateScoresForBatch
            return $submission; // Return original, which won't have calculation_details
        }
        // If $resultSubmission is found, it might have ->calculation_details populated by calculateScoresForBatch
        Log::debug("[SAWCalculatorService->calculate] Returning processed Submission ID: {$resultSubmission->id} with Final Score: {$resultSubmission->final_saw_score}. Normalized: ", (array)($resultSubmission->normalized_scores ?? []));
        if (isset($resultSubmission->calculation_details)) {
            Log::debug("[SAWCalculatorService->calculate] Calculation details for Submission ID {$resultSubmission->id}: ", $resultSubmission->calculation_details);
        }
        // session()->forget('saw_detail_page_submission_id'); // Already cleared in calculateScoresForBatch
        return $resultSubmission;
    }
}
