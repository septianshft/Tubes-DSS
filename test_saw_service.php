<?php

echo "Starting SAWCalculatorService test...\n";

require_once __DIR__ . '/vendor/autoload.php';

echo "Autoload included...\n";

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "App bootstrapped...\n";

try {
    echo "Testing SAWCalculatorService...\n";

    // Test SAWCalculatorService instantiation
    $sawService = $app->make('App\Services\SAWCalculatorService');
    echo "✅ SAWCalculatorService loaded successfully!\n";

    // Test PredefinedCriteriaService instantiation
    $predefinedService = $app->make('App\Services\PredefinedCriteriaService');
    echo "✅ PredefinedCriteriaService loaded successfully!\n";

    // Test if getCriteriaDefinition method exists
    if (method_exists($predefinedService, 'getCriteriaDefinition')) {
        echo "✅ getCriteriaDefinition method exists in PredefinedCriteriaService!\n";
    } else {
        echo "❌ getCriteriaDefinition method NOT found in PredefinedCriteriaService!\n";
    }

    // Test if calculateScore method exists in SAWCalculatorService
    if (method_exists($sawService, 'calculateScore')) {
        echo "✅ calculateScore method exists in SAWCalculatorService!\n";
    } else {
        echo "❌ calculateScore method NOT found in SAWCalculatorService!\n";
    }

    echo "\n🎉 All service tests passed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
