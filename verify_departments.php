<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Department;

$expected = [
    'Business Development',
    'Engineering',
    'Product Development',
    'Research Development',
    'Finance',
    'Training',
    'Business Administration'
];

$unexpected = [
    'Information Technology',
    'Human Resources',
    'Marketing',
    'Operations'
];

echo "--- START DEPARTMENT VERIFICATION ---" . PHP_EOL;

$allPassed = true;

// Check Expected
foreach ($expected as $name) {
    if (Department::where('name', $name)->exists()) {
        echo "[OK] Found: $name" . PHP_EOL;
    } else {
        echo "[FAIL] Missing: $name" . PHP_EOL;
        $allPassed = false;
    }
}

// Check Unexpected
foreach ($unexpected as $name) {
    if (Department::where('name', $name)->exists()) {
        echo "[FAIL] Unexpectedly Found: $name" . PHP_EOL;
        $allPassed = false;
    } else {
        echo "[OK] Absent: $name" . PHP_EOL;
    }
}

if ($allPassed) {
    echo "VERIFICATION_SUCCESS=true" . PHP_EOL;
} else {
    echo "VERIFICATION_SUCCESS=false" . PHP_EOL;
}
