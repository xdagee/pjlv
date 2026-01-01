<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\LeaveCalculatorService;
use App\Models\Holiday;
use Carbon\Carbon;

echo "--- START LEAVE CALCULATION VERIFICATION ---" . PHP_EOL;

// Setup: Create a fake holiday
// Let's assume today is Monday not a holiday.
// We will test a range that includes a weekend and a holiday.

// Define Test Range:
// Start: 2025-01-01 (Wednesday) - Holiday (New Year)
// End: 2025-01-07 (Tuesday)

// Calendar:
// 1st (Wed) - Holiday
// 2nd (Thu) - Work
// 3rd (Fri) - Work
// 4th (Sat) - Weekend
// 5th (Sun) - Weekend
// 6th (Mon) - Work
// 7th (Tue) - Work
// Expected Working Days: 4 days (2, 3, 6, 7)

// 1. Ensure Holiday Exists
Holiday::updateOrCreate(
    ['date' => '2025-01-01'],
    ['name' => 'New Year Verification Day']
);

$calculator = new LeaveCalculatorService();
$startDate = '2025-01-01';
$endDate = '2025-01-07';

$days = $calculator->calculateWorkingDays($startDate, $endDate);

echo "Testing Range: $startDate to $endDate" . PHP_EOL;
echo "Expected: 4 working days (Excluding Jan 1 Holiday and Jan 4-5 Weekend)" . PHP_EOL;
echo "Calculated: $days" . PHP_EOL;

if ($days === 4) {
    echo "VERIFICATION_SUCCESS=true" . PHP_EOL;
} else {
    echo "VERIFICATION_SUCCESS=false" . PHP_EOL;
}

// Cleanup
Holiday::where('date', '2025-01-01')->where('name', 'New Year Verification Day')->delete();
