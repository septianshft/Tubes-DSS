<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentSubmission;
use App\Models\Student;
use App\Models\ScholarshipBatch;
use App\Services\PredefinedCriteriaService;

echo "=== TESTING PREDEFINED CRITERIA DISPLAY ===\n";

// Get the submission
$submission = StudentSubmission::with(['student', 'scholarshipBatch'])->find(7);
if (!$submission) {
    echo "Submission ID 7 not found!\n";
    exit;
}

echo "Testing submission ID: {$submission->id}\n";
echo "Student: {$submission->student->name}\n";
echo "Batch: {$submission->scholarshipBatch->name}\n\n";

echo "=== RAW CRITERIA VALUES FROM SUBMISSION ===\n";
echo json_encode($submission->raw_criteria_values, JSON_PRETTY_PRINT) . "\n\n";

echo "=== NORMALIZED SCORES FROM SUBMISSION ===\n";
echo json_encode($submission->normalized_scores, JSON_PRETTY_PRINT) . "\n\n";

echo "=== BATCH CRITERIA CONFIG ===\n";
$criteriaConfig = $submission->scholarshipBatch->criteria_config;
echo json_encode($criteriaConfig, JSON_PRETTY_PRINT) . "\n\n";

echo "=== TESTING PREDEFINED CRITERIA VALUES ===\n";
$predefinedService = new PredefinedCriteriaService();

foreach ($criteriaConfig as $criterion) {
    if (!isset($criterion['type']) || $criterion['type'] !== 'predefined') {
        continue;
    }

    $criterionId = $criterion['id'];
    echo "Criterion ID: {$criterionId}\n";
    echo "Name: {$criterion['name']}\n";
    echo "Predefined Type: {$criterion['predefined_type']}\n";

    // Test getting raw value using similar logic to ShowSubmission
    $rawValue = null;
    $predefinedType = $criterion['predefined_type'] ?? null;

    // Display values based on predefined criteria configuration
    if ($predefinedType) {
        // Get the base definition for this predefined type
        $definition = $predefinedService->getCriteriaDefinition($predefinedType);

        if ($definition) {
            echo "  Definition Name: {$definition['name']}\\n";
            echo "  Definition Type: {$definition['type']}\\n";
            echo "  Definition Data Type: {$definition['data_type']}\\n";

            // Display the raw value used for this criterion from the submission
            if (isset($submission->raw_criteria_values[$criterionId])) {
                $currentRawValue = $submission->raw_criteria_values[$criterionId];
                echo "  Raw value (from submission->raw_criteria_values['{$criterionId}']): {$currentRawValue}\\n";
            } else {
                echo "  Raw value for '{$criterionId}' not found in submission->raw_criteria_values.\\n";
            }

            if ($definition['data_type'] === 'qualitative_option' && isset($definition['options'])) {
                echo "  Options (from definition):\\n";
                foreach ($definition['options'] as $opt) {
                    echo "    - {$opt['label']} (value: {$opt['value']}, numeric: {$opt['numeric_value']})\\n";
                }
            }
            // Optionally, display scoring rules if they are relevant to the test's purpose
            // if (isset($definition['scoring_rules'])) {
            //     echo "  Scoring Rules (from definition):\n";
            //     // Add logic here to pretty print scoring rules
            // }

        } else {
            echo "  Definition for predefined type '{$predefinedType}' not found.\\n";
        }
    } else {
        // This case might occur if $criterion['type'] === 'predefined' but 'predefined_type' is missing.
        // Or if $criterion['predefined_type'] is null or empty string.
        echo "  Criterion '{$criterionId}' is 'predefined' but 'predefined_type' is not set or invalid in batch config.\\n";
    }

    echo "Normalized score: " . ($submission->normalized_scores[$criterionId] ?? 'Not found') . "\\n";
    echo "---\n";
}
