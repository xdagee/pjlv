<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestLoginController extends LoginController
{
    public function testRedirect()
    {
        return $this->redirectTo();
    }
}

echo "--- START LOGIN REDIRECT VERIFICATION ---" . PHP_EOL;

// 1. Test Super Admin (ID 1)
$admin = User::find(1);
if ($admin) {
    Auth::login($admin);
    $controller = new TestLoginController();
    $redirect = $controller->testRedirect();

    if ($redirect === '/admin/dashboard') {
        echo "[OK] Admin User (ID 1) redirects to '$redirect'" . PHP_EOL;
    } else {
        echo "[FAIL] Admin User (ID 1) redirects to '$redirect', expected '/admin/dashboard'" . PHP_EOL;
    }
} else {
    echo "[FAIL] User 1 not found." . PHP_EOL;
}

// 2. Test Staff User (ID 2 - HR)
$staff = User::find(2); // HR created in seeders
if ($staff) {
    Auth::login($staff);
    $controller = new TestLoginController();
    $redirect = $controller->testRedirect();

    if ($redirect === '/dashboard') {
        echo "[OK] Staff User (ID 2) redirects to '$redirect'" . PHP_EOL;
    } else {
        echo "[FAIL] Staff User (ID 2) redirects to '$redirect', expected '/dashboard'" . PHP_EOL;
    }
} else {
    echo "[WARN] User 2 (Staff) not found. Skipping staff test." . PHP_EOL;
}
