<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Departments and Instruments Check ===\n\n";

// Get all departments
$departments = DB::table('departments')->get();
echo "Departments:\n";
foreach ($departments as $dept) {
    echo "  - ID {$dept->department_id}: {$dept->department_name}\n";
}

echo "\n";

// Get the instrument in the pending booking
$instrument = DB::table('instruments')
    ->join('departments', 'instruments.department_id', '=', 'departments.department_id')
    ->where('instruments.instrument_name', 'Microscope')
    ->select('instruments.*', 'departments.department_name')
    ->first();

if ($instrument) {
    echo "Microscope details:\n";
    echo "  - Instrument ID: {$instrument->instrument_id}\n";
    echo "  - Department ID: {$instrument->department_id}\n";
    echo "  - Department Name: {$instrument->department_name}\n";
}

echo "\n";

// Get staff with their departments
$staff = DB::table('users')
    ->join('roles', 'users.role_id', '=', 'roles.role_id')
    ->join('departments', 'users.department_id', '=', 'departments.department_id')
    ->where('roles.role_name', 'staff')
    ->select('users.*', 'departments.department_name')
    ->get();

echo "Staff members:\n";
foreach ($staff as $s) {
    echo "  - {$s->first_name} {$s->last_name}: Department ID {$s->department_id} ({$s->department_name})\n";
}

echo "\n=== Check Complete ===\n";
