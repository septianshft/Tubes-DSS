<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Services\SAWCalculatorService;
use App\Services\PredefinedCriteriaService;
use Illuminate\Support\Collection;

echo "🧪 Testing Fixed Ranking Logic for Tied Scores\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Create service instance
$predefinedService = new PredefinedCriteriaService();
$sawService = new SAWCalculatorService($predefinedService);

// Create test submissions with tied scores using reflection to access protected method
$submissions = new Collection([
    (object)[
        'id' => 1,
        'student' => (object)['name' => 'Alice'],
        'final_saw_score' => 0.8500, // Tied for 1st
    ],
    (object)[
        'id' => 2,
        'student' => (object)['name' => 'Bob'],
        'final_saw_score' => 0.8500, // Tied for 1st
    ],
    (object)[
        'id' => 3,
        'student' => (object)['name' => 'Charlie'],
        'final_saw_score' => 0.8500, // Tied for 1st
    ],
    (object)[
        'id' => 4,
        'student' => (object)['name' => 'David'],
        'final_saw_score' => 0.7500, // Should be rank 4
    ],
    (object)[
        'id' => 5,
        'student' => (object)['name' => 'Eve'],
        'final_saw_score' => 0.7000, // Should be rank 5
    ],
    (object)[
        'id' => 6,
        'student' => (object)['name' => 'Frank'],
        'final_saw_score' => 0.7000, // Tied for rank 5
    ],
]);

// Use reflection to access the protected rankSubmissions method
$reflection = new ReflectionClass($sawService);
$rankMethod = $reflection->getMethod('rankSubmissions');
$rankMethod->setAccessible(true);

// Test the ranking
$rankedSubmissions = $rankMethod->invoke($sawService, $submissions);

echo "📊 RANKING RESULTS:\n";
echo "-" . str_repeat("-", 50) . "\n";

foreach ($rankedSubmissions as $submission) {
    echo sprintf(
        "Rank %d: %s (Score: %.4f)\n",
        $submission->rank,
        $submission->student->name,
        $submission->final_saw_score
    );
}

echo "\n🎯 EXPECTED RESULTS:\n";
echo "-" . str_repeat("-", 30) . "\n";
echo "Rank 1: Alice (0.8500) ✓ Tied\n";
echo "Rank 1: Bob (0.8500) ✓ Tied\n";
echo "Rank 1: Charlie (0.8500) ✓ Tied\n";
echo "Rank 4: David (0.7500) ✓ Next rank after 3 tied students\n";
echo "Rank 5: Eve (0.7000) ✓ Next sequential rank\n";
echo "Rank 5: Frank (0.7000) ✓ Tied with Eve\n";

// Validation
$errors = [];

// Check tied scores at rank 1
$rank1Count = $rankedSubmissions->where('rank', 1)->count();
if ($rank1Count !== 3) {
    $errors[] = "❌ Expected 3 students at rank 1, got {$rank1Count}";
} else {
    echo "\n✅ PASS: 3 students correctly tied at rank 1\n";
}

// Check David's rank
$davidRank = $rankedSubmissions->where('student.name', 'David')->first()->rank;
if ($davidRank !== 4) {
    $errors[] = "❌ David should be rank 4, got rank {$davidRank}";
} else {
    echo "✅ PASS: David correctly ranked at 4\n";
}

// Check tied scores at rank 5
$rank5Count = $rankedSubmissions->where('rank', 5)->count();
if ($rank5Count !== 2) {
    $errors[] = "❌ Expected 2 students at rank 5, got {$rank5Count}";
} else {
    echo "✅ PASS: 2 students correctly tied at rank 5\n";
}

if (empty($errors)) {
    echo "\n🎉 ALL TESTS PASSED! Ranking logic is now working correctly.\n";
    echo "✅ Multiple students with identical scores receive the same rank\n";
    echo "✅ Subsequent ranks are properly adjusted for tied groups\n";
} else {
    echo "\n❌ TESTS FAILED:\n";
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🔧 FIXES APPLIED:\n";
echo "- Fixed rankSubmissions() method to properly handle ties\n";
echo "- Added JSON encoding for normalized_scores database storage\n";
echo "- Removed outdated tie-breaking comment\n";
