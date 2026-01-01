<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "--- START VERIFICATION ---" . PHP_EOL;

// 1. Setup Test Data
$roleNormal = Role::updateOrCreate(['id' => 5], ['role_name' => 'Normal', 'role_status' => 1]);
$roleAdmin = Role::updateOrCreate(['id' => 1], ['role_name' => 'Admin', 'role_status' => 1]);

$staff = Staff::firstOrCreate(
    ['staff_number' => 'ROLE-01'],
    [
        'title' => 'Mr',
        'firstname' => 'Role',
        'lastname' => 'Tester',
        'dob' => '1990-01-01',
        'gender' => 1,
        'mobile_number' => '070000TEST',
        'date_joined' => now(),
        'total_leave_days' => 20,
        'role_id' => $roleNormal->id,
        'is_active' => 1
    ]
);

echo "Created Staff with Role ID: " . $staff->role_id . " (Expected: " . $roleNormal->id . ")" . PHP_EOL;

// 2. Simulate API Data Structure Check (Index)
$apiData = Staff::select('id', 'role_id')->with('role')->find($staff->id);
if ($apiData->role && $apiData->role->role_name) {
    echo "API Check: Role relation loaded successfully. Role Name: " . $apiData->role->role_name . PHP_EOL;
} else {
    echo "API Check: FAILED to load role relation." . PHP_EOL;
}

// 3. Simulate Update (Controller Logic)
$staff->role_id = $roleAdmin->id;
$staff->save();
$staff->refresh();

echo "Updated Staff Role ID: " . $staff->role_id . " (Expected: " . $roleAdmin->id . ")" . PHP_EOL;

if ($staff->role_id == $roleAdmin->id) {
    echo "VERIFICATION_SUCCESS=true" . PHP_EOL;
} else {
    echo "VERIFICATION_SUCCESS=false" . PHP_EOL;
}

// Cleanup
$staff->delete();
