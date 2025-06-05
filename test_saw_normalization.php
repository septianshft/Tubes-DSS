<?php

// test_saw_normalization.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ScholarshipBatch;
use App\Models\Student;
use App\Models\StudentSubmission;
use App\Services\SAWCalculatorService;
use App\Services\PredefinedCriteriaService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// --- Configuration ---
$batchId = 99901; // Unique ID for this test batch
// Set to a specific submission ID (e.g., 101, 102, etc.) to enable detailed logging for that submission.
// The SAWCalculatorService looks for session('saw_detail_page_submission_id')
$enableDetailLoggingForSubmissionId = null; // Example: 101;

// --- Helper Functions ---
function print_header($text) { echo "\n--- {$text} ---\n"; }
function print_subheader($text) { echo "\n{$text}\n"; }
function dump_var($var, $label = '') { echo ($label ? $label . ': ' : '') . print_r($var, true) . "\n"; }

// --- Service Instantiation ---
/** @var SAWCalculatorService $sawCalculatorService */
$sawCalculatorService = $app->make(SAWCalculatorService::class);
/** @var PredefinedCriteriaService $predefinedCriteriaService */
$predefinedCriteriaService = $app->make(PredefinedCriteriaService::class);

// --- Test Data Setup ---

// 1. Scholarship Batch Configuration
print_header("SCHOLARSHIP BATCH CONFIGURATION");

$baseCriteriaConfig = [
    // Predefined with value_scale (Benefit)
    [
        'id' => 'academic_achievement_gpa',
        'name' => 'GPA',
        'weight' => 0.25,
        'type' => 'benefit',
        'is_predefined' => true,
        // 'value_scale' will be auto-populated from PredefinedCriteriaService
    ],
    // Predefined with value_scale (Cost)
    [
        'id' => 'economic_condition_parents_income',
        'name' => 'Parents Income',
        'weight' => 0.20,
        'type' => 'cost',
        'is_predefined' => true,
        // 'value_scale' will be auto-populated
    ],
    // Predefined with value_scale (Min=Max edge case for testing)
    [
        'id' => 'number_of_achievements_non_academic_national',
        'name' => 'National Achievements (Fixed Scale Test)',
        'weight' => 0.10,
        'type' => 'benefit',
        'is_predefined' => true,
        'value_scale' => ['min' => 5, 'max' => 5], // Explicitly set for min=max test
    ],
    // Predefined WITHOUT value_scale (will use cohort normalization)
    [
        'id' => 'number_of_siblings_dependents',
        'name' => 'Siblings/Dependents (Cohort Norm)',
        'weight' => 0.10,
        'type' => 'cost' // More siblings = higher "cost" in some scoring models, or benefit. Let's use cost.
                             // Predefined service has it as: {"0":5, "1":4, "2":3, "3":2, ">=4":1} (higher score better)
                             // So if type is cost, it means higher score (e.g. 5 for 0 siblings) is less costly.
                             // Let's align with PredefinedCriteriaService: score_map gives 1-5. Higher is better. So type should be benefit.
        'is_predefined' => true,
        'type' => 'benefit', // Corrected: higher score from map (1-5) is better.
        // NO 'value_scale' here - this forces cohort normalization on the 1-5 scores from its score_map
    ],
    // Custom Numeric (Benefit)
    [
        'id' => 'custom_projects_completed',
        'name' => 'Projects Completed',
        'weight' => 0.15,
        'type' => 'benefit',
        'is_predefined' => false,
    ],
    // Custom Qualitative with value_map (Benefit)
    [
        'id' => 'custom_leadership_exp',
        'name' => 'Leadership Experience',
        'weight' => 0.10,
        'type' => 'benefit',
        'is_predefined' => false,
        'value_map' => [
            'None' => 1,
            'Participant' => 2,
            'Leader' => 3,
            'Coordinator' => 4,
            'Extensive' => 5,
        ],
    ],
    // Custom Numeric (Benefit, for single student / min=max cohort test)
    [
        'id' => 'custom_volunteer_hours',
        'name' => 'Volunteer Hours',
        'weight' => 0.10,
        'type' => 'benefit',
        'is_predefined' => false,
    ],
];

// Fetch actual value_scale and other details for predefined criteria from PredefinedCriteriaService
// $allPredefinedFullConfig = $predefinedCriteriaService->getPredefinedCriteriaConfig(); // This was incorrect
$finalCriteriaConfig = [];
foreach ($baseCriteriaConfig as $criterion) {
    if ($criterion['is_predefined']) {
        // For predefined criteria, we need to fetch their full configuration using their ID (key)
        // and the weight assigned in this specific test setup ($criterion['weight']).
        $fullConfig = $predefinedCriteriaService->getPredefinedCriteriaConfig($criterion['id'], $criterion['weight']);
        if ($fullConfig) {
            // Merge: test config can override or add specific test values if needed.
            // For example, a test might want to force a specific 'value_scale'
            // or ensure 'options' are correctly handled.
            $finalCriterion = array_merge($fullConfig, $criterion);
            // Ensure the original weight from $baseCriteriaConfig is used if not overridden by $fullConfig (it shouldn't be)
            $finalCriterion['weight'] = $criterion['weight'];
            $finalCriteriaConfig[] = $finalCriterion;
        } else {
            echo "WARNING: Could not retrieve predefined config for ID: {$criterion['id']}\n";
            // Add the base criterion if full config not found, though this might indicate an issue
            $finalCriteriaConfig[] = $criterion;
        }
    } else {
        // For custom criteria, the definition in $baseCriteriaConfig is complete
        $finalCriteriaConfig[] = $criterion;
    }
}

// Recalculate total weight
$totalWeight = round(array_sum(array_column($finalCriteriaConfig, 'weight')), 2);
dump_var($totalWeight, "Total Weight of Criteria");
if ($totalWeight !== 1.0) {
    echo "Warning: Total criteria weight is {$totalWeight}, not 1.0. Scores might not be percentage-based.\n";
}

dump_var($finalCriteriaConfig, "Final Criteria Configuration for Batch");

$batch = new ScholarshipBatch([
    'id' => $batchId,
    'name' => 'Test Batch for Normalization Logic',
    'criteria_config' => $finalCriteriaConfig,
    // Add other necessary fields if your service/model expects them
    'status' => 'open',
    'submission_deadline' => now()->addDays(7),
    'created_at' => now(),
    'updated_at' => now(),
]);


// 2. Students and Submissions
print_header("STUDENT SUBMISSIONS DATA");
$studentsData = [
    // Student 1: Alice
    ['id' => 101, 'name' => 'Alice', 'submissions_data' => [
        'academic_achievement_gpa' => 3.8,                            // Predefined, value_scale (0-4), benefit
        'economic_condition_parents_income' => 5000000,               // Predefined, value_scale (0-20M), cost
        'number_of_achievements_non_academic_national' => '>=4',      // Predefined, test min=max value_scale (5-5), benefit. Score from map: 5
        'number_of_siblings_dependents' => '0',                       // Predefined, no value_scale (use cohort), benefit. Score from map: 5
        'custom_projects_completed' => 5,                             // Custom numeric, benefit
        'custom_leadership_exp' => 'Extensive',                       // Custom qualitative (map to 5), benefit
        'custom_volunteer_hours' => 50,                               // Custom numeric, benefit
    ]],
    // Student 2: Bob
    ['id' => 102, 'name' => 'Bob', 'submissions_data' => [
        'academic_achievement_gpa' => 3.5,
        'economic_condition_parents_income' => 15000000,
        'number_of_achievements_non_academic_national' => '1',        // Score from map: 2. For min=max (5-5) scale test.
        'number_of_siblings_dependents' => '2',                       // Score from map: 3
        'custom_projects_completed' => 3,
        'custom_leadership_exp' => 'Leader',                          // Mapped to 3
        'custom_volunteer_hours' => 50,                               // Same as Alice for cohort min=max test
    ]],
    // Student 3: Charlie (with some missing data)
    ['id' => 103, 'name' => 'Charlie', 'submissions_data' => [
        'academic_achievement_gpa' => 2.9,
        'economic_condition_parents_income' => 2000000,
        // 'number_of_achievements_non_academic_national' => MISSING    // Test missing predefined
        'number_of_siblings_dependents' => '>=4',                      // Score from map: 1
        'custom_projects_completed' => 1,
        'custom_leadership_exp' => 'None',                            // Mapped to 1
        // 'custom_volunteer_hours' => MISSING                          // Test missing custom
    ]],
    // Student 4: David (to test single value cohort for volunteer hours if others miss it)
    ['id' => 104, 'name' => 'David', 'submissions_data' => [
        'academic_achievement_gpa' => 3.0,
        'economic_condition_parents_income' => 7000000,
        'number_of_achievements_non_academic_national' => '3',        // Score from map: 4
        'number_of_siblings_dependents' => '1',                       // Score from map: 4
        'custom_projects_completed' => 2,
        'custom_leadership_exp' => 'Participant',                     // Mapped to 2
        'custom_volunteer_hours' => 100,                              // Unique value, others might be 50 or missing
    ]],
];

$submissions = new Collection();
$studentModels = new Collection(); // Keep track of student models for relations

foreach ($studentsData as $sIdx => $studentData) {
    $student = new Student(['id' => $studentData['id'], 'name' => $studentData['name']]);
    // Mock student attributes for predefined criteria if they are fetched from student model as fallback
    foreach ($finalCriteriaConfig as $crit) {
        if ($crit['is_predefined'] && isset($studentData['submissions_data'][$crit['id']]) && isset($crit['student_attribute'])) {
            $student->{$crit['student_attribute']} = $studentData['submissions_data'][$crit['id']];
        }
    }
    $studentModels->put($studentData['id'], $student);

    $submission = new StudentSubmission([
        'id' => $studentData['id'], // Using student ID as submission ID for simplicity in test
        'student_id' => $studentData['id'],
        'scholarship_batch_id' => $batch->id,
        'raw_criteria_values' => $studentData['submissions_data'],
        'status' => 'submitted',
        'created_at' => now()->addMinutes($sIdx), // Ensure unique timestamps for tie-breaking
        'updated_at' => now()->addMinutes($sIdx),
    ]);
    $submission->setRelation('student', $student);
    $submission->setRelation('scholarshipBatch', $batch);
    $submissions->push($submission);

    dump_var($studentData['submissions_data'], "Raw Submission Data for Student {$studentData['name']} (ID: {$studentData['id']})");
}

// --- Execute SAW Calculation ---
print_header("SAW CALCULATION RESULTS");

if ($enableDetailLoggingForSubmissionId) {
    session(['saw_detail_page_submission_id' => $enableDetailLoggingForSubmissionId]);
    echo "Detailed logging enabled for Submission ID: {$enableDetailLoggingForSubmissionId}\n";
    Log::setDefaultDriver('stack'); // Ensure logs go to default (e.g. file)
    Log::info("Test Script: Detailed logging enabled for Submission ID: {$enableDetailLoggingForSubmissionId}");
}

$processedSubmissions = $sawCalculatorService->calculateScoresForBatch($batch, $submissions);

// --- Verify Results ---
print_subheader("Processed Submissions with Scores:");
foreach ($processedSubmissions as $sub) {
    $studentName = $studentModels->get($sub->student_id)->name ?? 'Unknown Student';
    echo "\nStudent ID: {$sub->student_id} (Submission ID: {$sub->id}), Name: {$studentName}\n";
    echo "  Final SAW Score: {$sub->final_saw_score}\n";
    echo "  Normalized Scores (raw_criteria_value -> numeric_value_for_calc -> normalized_value_stored):\n";

    foreach (($sub->normalized_scores ?? []) as $critId => $normScore) {
        $critDetails = collect($finalCriteriaConfig)->firstWhere('id', $critId);
        $critName = $critDetails['name'] ?? $critId;
        $rawValue = $sub->raw_criteria_values[$critId] ?? 'MISSING';
        $numericValForCalc = 'N/A'; // Will try to find from calculation_details if present

        if (isset($sub->calculation_details['steps'])) {
            $stepDetail = collect($sub->calculation_details['steps'])->firstWhere('criterion_id', $critId);
            if ($stepDetail) {
                $numericValForCalc = $stepDetail['numeric_value_for_calc'] ?? 'N/A';
            }
        }
        echo "    - {$critName} ({$critId}): Raw='{$rawValue}' -> Numeric='{$numericValForCalc}' -> Normalized={$normScore}\n";
    }

    if (isset($sub->calculation_details['steps'])) {
        echo "  Calculation Details (from log if enabled for this submission ID):
";
        foreach($sub->calculation_details['steps'] as $step) {
            echo "    Criterion: {$step['criterion_name']} ({$step['criterion_id']})\n";
            echo "      Raw Value Submitted: '{$step['raw_value_submitted']}', Numeric Value for Calc: {$step['numeric_value_for_calc']}\n";
            echo "      Normalization Method: {$step['normalization_details']['method']}, Scale Min: {$step['normalization_details']['scale_min']}, Scale Max: {$step['normalization_details']['scale_max']}\n";
            echo "      Normalization Formula: {$step['normalization_formula_string']}\n";
            echo "      Normalized (Stored): {$step['normalized_value_stored']}, Weight: {$step['criterion_weight']}, Contribution: {$step['weighted_score_contribution']}\n";
        }
         echo "  Calculation Summary (from log):
";
         dump_var($sub->calculation_details['summary'] ?? 'Not available');
    }
}

print_header("EXPECTED NORMALIZATION VALUES (Manual Calculation for Verification)");
echo "Note: These are simplified calculations. Check service logic for exact min/max from cohort and score mapping.\n";

// --- Expected values for specific criteria for Alice (ID 101) ---
// GPA (3.8 on 0-4, benefit): (3.8 - 0) / (4 - 0) = 0.95
// Income (5M on 0-20M, cost): (20M - 5M) / (20M - 0M) = 15M / 20M = 0.75
// National Achievements ('>=4' -> score 5, on 5-5 scale, benefit): Should be 1.0
// Siblings ('0' -> score 5, cohort norm, benefit):
//   Scores: Alice(5), Bob(3), Charlie(1), David(4). Min=1, Max=5.
//   Alice: (5-1)/(5-1) = 1.0
// Custom Projects (5, cohort [5,3,1,2], benefit): Min=1, Max=5.
//   Alice: (5-1)/(5-1) = 1.0
// Leadership ('Extensive' -> 5, cohort [5,3,1,2], benefit): Min=1, Max=5.
//   Alice: (5-1)/(5-1) = 1.0
// Volunteer Hours (50, cohort [50,50,100] (Charlie missing), benefit): Min=50, Max=100.
//   Alice: (50-50)/(100-50) = 0.0

// --- Expected for Bob (ID 102) ---
// National Achievements ('1' -> score 2, on 5-5 scale, benefit): Should be 1.0 (as scaleMax == scaleMin)
// Volunteer Hours (50, cohort [50,50,100], benefit): Min=50, Max=100.
//   Bob: (50-50)/(100-50) = 0.0

// --- Expected for Charlie (ID 103) ---
// National Achievements: MISSING -> numeric value null -> normalized 0
// Volunteer Hours: MISSING -> numeric value null -> normalized 0

// --- Expected for David (ID 104) ---
// Volunteer Hours (100, cohort [50,50,100], benefit): Min=50, Max=100.
//   David: (100-50)/(100-50) = 1.0

echo "\n\nTest script finished. Check laravel.log if detailed logging was enabled and output here is not sufficient.\n";

// Clear session flag if it was set
if ($enableDetailLoggingForSubmissionId) {
    session()->forget('saw_detail_page_submission_id');
    Log::info("Test Script: Cleared saw_detail_page_submission_id from session.");
}

?>
