<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ScholarshipBatch;
use App\Models\StudentSubmission;
use App\Services\SAWCalculatorService;

echo "Testing SAW Calculator Service Fix\n";
echo "==================================\n\n";

// Get batch ID from command line argument or use default
$batchId = isset($argv[1]) ? (int)$argv[1] : 1;

// Get the specified batch
$batch = ScholarshipBatch::find($batchId);
if (!$batch) {
    echo "Scholarship batch with ID {$batchId} not found!\n";
    exit(1);
}

echo "Testing with Batch: {$batch->name} (ID: {$batch->id})\n";

// Get submissions for this batch
$submissions = StudentSubmission::where('scholarship_batch_id', $batch->id)->limit(3)->get();
if ($submissions->isEmpty()) {
    echo "No submissions found for this batch!\n";
    exit(1);
}

echo "Found " . $submissions->count() . " submissions to test\n\n";

// Test the SAW calculation using the correct calculate method
$predefinedCriteriaService = new \App\Services\PredefinedCriteriaService();
$sawService = new SAWCalculatorService($predefinedCriteriaService);

echo "SAW Calculation Results:\n";
echo "========================\n";

foreach ($submissions as $submission) {
    echo "Testing submission ID: {$submission->id}\n";

    // Calculate using the individual calculateScore method which handles detailed logging internally
    $calculatedSubmission = $sawService->calculateScore($submission);

    echo "Submission ID: {$calculatedSubmission->id}\n";
    echo "Final SAW Score: {$calculatedSubmission->final_saw_score}\n";

    // Check if normalized_scores property exists and is populated
    if (isset($calculatedSubmission->normalized_scores) && is_array($calculatedSubmission->normalized_scores)) {
        echo "Normalized Scores Count: " . count($calculatedSubmission->normalized_scores) . "\n";

        // Show all normalized scores for verification
        foreach ($calculatedSubmission->normalized_scores as $criterionId => $normalizedScore) {
            echo "  Criterion {$criterionId}: {$normalizedScore}\n";
        }
    } else {
        echo "❌ ERROR: normalized_scores not found or not array!\n";
    }

    // Check calculation details using the service method
    $calculationSteps = $sawService->getCalculationStepsForSubmission($calculatedSubmission->id);
    if ($calculationSteps && is_array($calculationSteps)) {
        echo "Calculation Details: Available via service method\n";
        if (isset($calculationSteps['steps'])) {
            echo "  Steps Count: " . count($calculationSteps['steps']) . "\n";

            // VERIFICATION: Compare normalized_scores with calculation steps
            echo "\n🔍 VERIFICATION: Comparing normalized_scores vs calculation steps:\n";
            foreach ($calculationSteps['steps'] as $step) {
                $criterionId = $step['criterion_id'] ?? 'unknown';
                $normalizedFromFormula = $step['normalized_value_after_clamping'] ?? 'N/A';
                $normalizedFromScores = $calculatedSubmission->normalized_scores[$criterionId] ?? 'N/A';

                echo "  Criterion {$criterionId}:\n";
                echo "    normalized_scores[{$criterionId}]: {$normalizedFromScores}\n";
                echo "    calculation_steps.normalized_value_after_clamping: {$normalizedFromFormula}\n";

                // Check if they match (considering the normalized_scores is rounded to 4 decimal places)
                if (is_numeric($normalizedFromScores) && is_numeric($normalizedFromFormula)) {
                    // normalized_scores should be the rounded version of normalized_value_after_clamping
                    $expectedRounded = round((float)$normalizedFromFormula, 4);
                    $actualNormalized = (float)$normalizedFromScores;
                    $diff = abs($expectedRounded - $actualNormalized);

                    if ($diff < 0.0001) { // Allow for tiny floating point differences
                        echo "    ✅ MATCH (normalized_scores is properly rounded from formula)\n";
                        echo "    Formula result: {$normalizedFromFormula}, Rounded for storage: {$expectedRounded}, Stored: {$actualNormalized}\n";
                    } else {
                        echo "    ❌ MISMATCH! Expected rounded: {$expectedRounded}, Actual stored: {$actualNormalized} (diff: {$diff})\n";
                    }
                } else {
                    if ($normalizedFromScores === $normalizedFromFormula) {
                        echo "    ✅ MATCH (both non-numeric: {$normalizedFromScores})\n";
                    } else {
                        echo "    ❌ MISMATCH! Different values or types\n";
                        echo "    normalized_scores: " . var_export($normalizedFromScores, true) . "\n";
                        echo "    normalized_value_after_clamping: " . var_export($normalizedFromFormula, true) . "\n";
                    }
                }
                echo "\n";
            }
        }
        if (isset($calculationSteps['summary'])) {
            echo "  Summary: Available\n";
        }
    } else {
        echo "❌ ERROR: calculation steps not found via service method!\n";
    }

    echo "=" . str_repeat("=", 60) . "\n\n";
}

echo "Test completed successfully! ✅\n";
