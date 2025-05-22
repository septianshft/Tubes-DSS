<?php

namespace App\Services;

use App\Models\StudentSubmission;
use App\Models\ScholarshipBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SAWCalculatorService
{
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
                    continue;
                }
                $criterionId = $criterion['id'];
                $criterionType = $criterion['type'];
                $criterionWeight = (float)($criterion['weight'] ?? 0);

                // Ambil nilai siswa untuk kriteria.
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
                    $submissionNormalizedScores[$criterionId] = 0;
                    continue;
                }

                // Validasi nilai min max
                if (!isset($minMaxValues[$criterionId])) {
                    Log::error("SAWCalculatorService: Nilai min/max tidak ditemukan untuk ID kriteria {$criterionId} di batch {$batch->id}");
                    $submissionNormalizedScores[$criterionId] = 0;
                    continue;
                }


                $minValue = $minMaxValues[$criterionId]['min'];
                $maxValue = $minMaxValues[$criterionId]['max'];
                $normalizedValue = 0;

                // --- Logika Normalisasi ---
                if ($maxValue == $minValue) {
                    // Jika semua nilai sama, tidak bisa normalisasi.
                    // Maka set nilai normalisasi ke 0 atau 1.
                    $normalizedValue = ($maxValue == 0) ? 0 : 1;
                } else {
                    // Rumus normalisasi standar:
                    if ($criterionType === 'benefit') {
                        // (currentValue - minValue) / (maxValue - minValue)
                        $normalizedValue = ($currentValue - $minValue) / ($maxValue - $minValue);
                    } elseif ($criterionType === 'cost') {
                        // (maxValue - currentValue) / (maxValue - minValue)
                        $normalizedValue = ($maxValue - $currentValue) / ($maxValue - $minValue);
                    }
                }
                // --- Akhir Logika Normalisasi ---

                // Validasi nilai normalisasi berada dalam rentang 0-1.
                $normalizedValue = max(0, min(1, $normalizedValue));

                $submissionNormalizedScores[$criterionId] = round($normalizedValue, 4);

                $finalScore += $normalizedValue * $criterionWeight;
            }


            $submission->normalized_scores = $submissionNormalizedScores;
            $submission->final_saw_score = round($finalScore, 4);


            if ($logForThisSubmission) {
                Log::debug("[SAWCalculatorService->calculateScoresForBatch] Calculated scores for (detail page) Submission ID: {$submission->id}. Normalized: ", $submissionNormalizedScores, ["Final Score" => $submission->final_saw_score]);
            }

        }
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
            return $submission;
        }
        Log::debug("[SAWCalculatorService->calculate] Returning processed Submission ID: {$resultSubmission->id} with Final Score: {$resultSubmission->final_saw_score}. Normalized: ", $resultSubmission->normalized_scores);
        // session()->forget('saw_detail_page_submission_id'); // Already cleared in calculateScoresForBatch
        return $resultSubmission;
    }
}
