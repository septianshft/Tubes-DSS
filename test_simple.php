<?php
echo "Testing Simple Script\n";

require_once __DIR__ . '/vendor/autoload.php';
echo "Autoload loaded\n";

$app = require_once __DIR__ . '/bootstrap/app.php';
echo "App loaded\n";

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "Kernel bootstrapped\n";

use App\Models\StudentSubmission;
echo "Model imported\n";

$submission = StudentSubmission::find(16);
echo "Submission found: " . ($submission ? $submission->id : 'not found') . "\n";
echo "Test completed!\n";
