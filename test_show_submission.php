<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentSubmission;
use App\Models\ScholarshipBatch;
use App\Services\SAWCalculatorService;
use App\Services\PredefinedCriteriaService;

echo "Testing ShowSubmission Component Logic\n";
echo "=====================================\n";

// Get submission 16 from batch 4
$submission = StudentSubmission::find(16);
$batch = ScholarshipBatch::find(4);

if (!$submission || !$batch) {
    echo "Submission 16 or Batch 4 not found!\n";
    exit(1);
}

echo "Testing with Submission: {$submission->id} (Student ID: {$submission->student_id})\n";
echo "Batch: {$batch->name} (ID: {$batch->id})\n\n";

// Initialize services
$predefinedService = new PredefinedCriteriaService();
$sawService = new SAWCalculatorService($predefinedService);

// Test the raw value fetching logic for predefined criteria
echo "Testing Predefined Criteria Value Fetching:\n";
echo "==========================================\n";

$criteriaDefinitions = $predefinedService->getCriteriaDefinition();

foreach ($criteriaDefinitions as $criterionId => $criterion) {
    echo "\nCriterion: {$criterionId}\n";
    echo "Name: {$criterion['name']}\n";
    echo "Type: {$criterion['data_type']}\n";

    // Test the public method we created
    $rawValue = $sawService->getStudentInputValueForPredefinedCriterion($submission, $criterionId);
    echo "Raw Value: " . var_export($rawValue, true) . "\n";

    if ($criterion['data_type'] === 'qualitative_option' && isset($criterion['options'])) {
        if (isset($criterion['options'][$rawValue])) {
            echo "Display Label: {$criterion['options'][$rawValue]['label']}\n";
        } else {
            echo "Display Label: [No label found for '{$rawValue}']\n";
        }
    }
    echo "---\n";
}

echo "\nTesting Complete!\n";
