<?php

$files = [
    'd:\Documents\GitHub\pjlv\app\Http\Controllers\DepartmentsController.php',
    'd:\Documents\GitHub\pjlv\resources\views\admin\departments\index.blade.php',
    'd:\Documents\GitHub\pjlv\resources\views\admin\departments\create.blade.php',
    'd:\Documents\GitHub\pjlv\resources\views\admin\departments\edit.blade.php',
    'd:\Documents\GitHub\pjlv\resources\views\layouts\admin_sidebar.blade.php',
];

$allFound = true;

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "[OK] Found: " . basename($file) . PHP_EOL;
    } else {
        echo "[FAIL] Missing: " . basename($file) . PHP_EOL;
        $allFound = false;
    }
}

// Check Sidebar Link
$sidebarContent = file_get_contents('d:\Documents\GitHub\pjlv\resources\views\layouts\admin_sidebar.blade.php');
if (strpos($sidebarContent, 'admin/departments') !== false) {
    echo "[OK] Sidebar link found." . PHP_EOL;
} else {
    echo "[FAIL] Sidebar link missing." . PHP_EOL;
    $allFound = false;
}

// Check Controller Methods
$controllerContent = file_get_contents('d:\Documents\GitHub\pjlv\app\Http\Controllers\DepartmentsController.php');
if (strpos($controllerContent, 'function create()') !== false && strpos($controllerContent, 'function edit(') !== false) {
    echo "[OK] Controller methods (create/edit) found." . PHP_EOL;
} else {
    echo "[FAIL] Controller methods missing." . PHP_EOL;
    $allFound = false;
}

if ($allFound) {
    echo "VERIFICATION_SUCCESS=true" . PHP_EOL;
} else {
    echo "VERIFICATION_SUCCESS=false" . PHP_EOL;
}
