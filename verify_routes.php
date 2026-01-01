<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "--- START ROUTE VERIFICATION ---" . PHP_EOL;

$routes = [
    'leavetypes.index',
    'leavetypes.create',
    'leavetypes.store',
    'leavetypes.edit',
    'leavetypes.update',
    'leavetypes.destroy',
    'holidays.index', // Verification of others
    'leavelevels.index'
];

foreach ($routes as $route) {
    if (Route::has($route)) {
        echo "[OK] Route '$route' exists." . PHP_EOL;
    } else {
        echo "[FAIL] Route '$route' does NOT exist." . PHP_EOL;
    }
}
echo "VERIFICATION_COMPLETE" . PHP_EOL;
