<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "--- START SEPARATION VERIFICATION ---" . PHP_EOL;

// 1. Fetch User 1 (Should be Admin)
$user = User::with(['admin', 'staff'])->find(1);

if (!$user) {
    echo "[FAIL] User ID 1 not found." . PHP_EOL;
    exit(1);
}

// 2. Check Admin Relation
if ($user->admin) {
    echo "[OK] User ID 1 has 'admin' profile: " . $user->admin->name . PHP_EOL;
} else {
    echo "[FAIL] User ID 1 is MISSING 'admin' profile." . PHP_EOL;
}

// 3. Check Staff Relation (Should be null or empty)
if (!$user->staff) {
    echo "[OK] User ID 1 has NO 'staff' profile (Correct)." . PHP_EOL;
} else {
    echo "[FAIL] User ID 1 STILL has 'staff' profile (Cleanup failed?)" . PHP_EOL;
    // Note: If you didn't delete the staff record in migration, this will fail.
}

// 4. Test Middleware Logic Simulation
Auth::login($user);
if (Auth::user()->admin) {
    echo "[OK] CheckSuperAdmin Logic: Auth::user()->admin is true." . PHP_EOL;
} else {
    echo "[FAIL] CheckSuperAdmin Logic: Auth::user()->admin is false." . PHP_EOL;
}

echo "VERIFICATION_COMPLETE" . PHP_EOL;
