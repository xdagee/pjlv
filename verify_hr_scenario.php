<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hrCount = \App\Models\Staff::where('role_id', 2)->count();
$hr = \App\Models\Staff::where('staff_number', 'HR-001')->first();
$supervisedCount = 0;
if ($hr) {
    $supervisedCount = \App\Models\Staff::where('supervisor_id', $hr->id)->count();
}

echo "HR_COUNT=" . $hrCount . PHP_EOL;
if ($hr) {
    echo "HR_ID=" . $hr->id . PHP_EOL;
    echo "HR_NAME=" . $hr->firstname . ' ' . $hr->lastname . PHP_EOL;
}
echo "SUPERVISED_STAFF_COUNT=" . $supervisedCount . PHP_EOL;

if ($hrCount >= 1 && $supervisedCount >= 10) {
    echo "VERIFICATION_SUCCESS=true" . PHP_EOL;
} else {
    echo "VERIFICATION_SUCCESS=false" . PHP_EOL;
}
