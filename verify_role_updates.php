<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;
use App\Enums\RoleEnum;

echo "--- START ROLE UPDATE VERIFICATION ---" . PHP_EOL;

// 1. Check Database Names
$roles = Role::all()->keyBy('id');

$checks = [
    1 => 'Super Administrator',
    2 => 'Human Resource Manager',
    3 => 'Chief Executive Officer',
    4 => 'Operations Manager',
    5 => 'Head of Department',
    6 => 'Normal'
];

$success = true;

foreach ($checks as $id => $expectedName) {
    if (isset($roles[$id]) && $roles[$id]->role_name === $expectedName) {
        echo "[OK] Role ID $id is '$expectedName'" . PHP_EOL;
    } else {
        echo "[FAIL] Role ID $id expected '$expectedName' but found '" . ($roles[$id]->role_name ?? 'MISSING') . "'" . PHP_EOL;
        $success = false;
    }
}

// 2. Check Enum Logic
$testEnum = RoleEnum::fromName('super administrator');
if ($testEnum === RoleEnum::ADMIN) {
    echo "[OK] RoleEnum::fromName('super admin') mapping correct." . PHP_EOL;
} else {
    echo "[FAIL] RoleEnum::fromName('super administrator') mapping failed." . PHP_EOL;
    $success = false;
}

$testEnum2 = RoleEnum::fromName('HOD');
if ($testEnum2 === RoleEnum::HOD) {
    echo "[OK] RoleEnum::fromName('HOD') mapping correct." . PHP_EOL;
} else {
    echo "[FAIL] RoleEnum::fromName('HOD') mapping failed." . PHP_EOL;
    $success = false;
}


if ($success) {
    echo "VERIFICATION_SUCCESS=true" . PHP_EOL;
} else {
    echo "VERIFICATION_SUCCESS=false" . PHP_EOL;
}
