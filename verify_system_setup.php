<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Holiday;
use App\Models\LeaveLevel;

echo "--- START SYSTEM SETUP VERIFICATION ---" . PHP_EOL;

// 1. Holiday CRUD
try {
    echo "Creating Holiday..." . PHP_EOL;
    $holiday = Holiday::create(['name' => 'Test Holiday', 'date' => '2025-12-25']);
    echo "[OK] Created Holiday ID: " . $holiday->id . PHP_EOL;

    echo "Updating Holiday..." . PHP_EOL;
    $holiday->update(['name' => 'Updated Holiday']);
    if ($holiday->fresh()->name === 'Updated Holiday') {
        echo "[OK] Updated Holiday Name" . PHP_EOL;
    } else {
        echo "[FAIL] Update Holiday Failed" . PHP_EOL;
    }

    echo "Deleting Holiday..." . PHP_EOL;
    $holiday->delete();
    if (!Holiday::find($holiday->id)) {
        echo "[OK] Deleted Holiday" . PHP_EOL;
    } else {
        echo "[FAIL] Delete Holiday Failed" . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "[FAIL] Holiday CRUD Error: " . $e->getMessage() . PHP_EOL;
}

// 2. Leave Level CRUD
try {
    echo "Creating Leave Level..." . PHP_EOL;
    $level = LeaveLevel::create(['level_name' => 'Test Level', 'annual_leave_days' => 10]);
    echo "[OK] Created Level ID: " . $level->id . PHP_EOL;

    echo "Updating Leave Level..." . PHP_EOL;
    $level->update(['annual_leave_days' => 15]);
    if ($level->fresh()->annual_leave_days == 15) {
        echo "[OK] Updated Level Days" . PHP_EOL;
    } else {
        echo "[FAIL] Update Level Failed" . PHP_EOL;
    }

    echo "Deleting Leave Level..." . PHP_EOL;
    $level->delete();
    if (!LeaveLevel::find($level->id)) {
        echo "[OK] Deleted Level" . PHP_EOL;
    } else {
        echo "[FAIL] Delete Level Failed" . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "[FAIL] Leave Level CRUD Error: " . $e->getMessage() . PHP_EOL;
}

echo "VERIFICATION_COMPLETE" . PHP_EOL;
