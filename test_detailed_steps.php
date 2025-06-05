<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\SAWCalculatorService;
use App\Services\PredefinedCriteriaService;
use App\Models\StudentSubmission;
use App\Models\ScholarshipBatch;

echo "Testing Detailed Calculation Steps Retrieval\n";
echo "=============================================\n\n";

// Find a batch with submissions
$batch = ScholarshipBatch::whereNotNull('criteria_config')
    ->whereHas('submissions')
    ->first();

if (!$batch) {
    echo "No batch with criteria config and submissions found\n";
    exit(1);
}

echo "Testing with Batch ID: {$batch->id}\n";
echo "Batch name: {$batch->name}\n\n";

// Get a submission
$submission = $batch->submissions()->first();
if (!$submission) {
    echo "No submissions found for batch\n";
    exit(1);
}

echo "Testing with Submission ID: {$submission->id}\n";
echo "Student ID: {$submission->student_id}\n\n";

// Initialize services
$predefinedCriteriaService = new PredefinedCriteriaService();
$sawService = new SAWCalculatorService($predefinedCriteriaService);

// Calculate scores (this should store detailed steps in session)
echo "Step 1: Calculating SAW score...\n";
$updatedSubmission = $sawService->calculateScore($submission);

echo "Final SAW Score: " . ($updatedSubmission->final_saw_score ?? 'NULL') . "\n";
echo "Normalized Scores count: " . (count($updatedSubmission->normalized_scores ?? []) ) . "\n\n";

// Now try to retrieve the detailed calculation steps
echo "Step 2: Retrieving detailed calculation steps...\n";
$calculationSteps = $sawService->getCalculationStepsForSubmission($submission->id);

if ($calculationSteps) {
    echo "SUCCESS: Calculation steps retrieved!\n\n";
    echo "Structure:\n";
    echo "- Has 'steps' array: " . (isset($calculationSteps['steps']) ? 'YES (' . count($calculationSteps['steps']) . ' steps)' : 'NO') . "\n";
    echo "- Has 'summary' array: " . (isset($calculationSteps['summary']) ? 'YES' : 'NO') . "\n";

    if (isset($calculationSteps['steps']) && count($calculationSteps['steps']) > 0) {
        echo "\nFirst calculation step example:\n";
        $firstStep = $calculationSteps['steps'][0];
        echo "- Criterion ID: " . ($firstStep['criterion_id'] ?? 'N/A') . "\n";
        echo "- Criterion Name: " . ($firstStep['criterion_name'] ?? 'N/A') . "\n";
        echo "- Raw Value: " . ($firstStep['raw_value_submitted'] ?? 'N/A') . "\n";
        echo "- Normalized Value: " . ($firstStep['normalized_value_after_clamping'] ?? 'N/A') . "\n";
        echo "- Weighted Contribution: " . ($firstStep['weighted_score_contribution'] ?? 'N/A') . "\n";
    }

    if (isset($calculationSteps['summary'])) {
        echo "\nSummary:\n";
        echo "- Total Weighted Score: " . ($calculationSteps['summary']['total_weighted_score_from_steps'] ?? 'N/A') . "\n";
        echo "- Final SAW Score: " . ($calculationSteps['summary']['final_saw_score_rounded'] ?? 'N/A') . "\n";
    }
} else {
    echo "ERROR: No calculation steps found!\n";
    echo "This means the detailed steps are not being stored or retrieved properly.\n";
}

echo "\n=== Test Complete ===\n";
