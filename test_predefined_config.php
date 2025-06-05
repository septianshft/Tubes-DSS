<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Services\PredefinedCriteriaService;

$service = new PredefinedCriteriaService();
$config = $service->getPredefinedCriteriaConfig('class_attendance_percentage', 0.3);

echo "Predefined Criteria Config:\n";
print_r($config);

echo "\nDescription field: " . ($config['description'] ?? 'NOT FOUND') . "\n";
