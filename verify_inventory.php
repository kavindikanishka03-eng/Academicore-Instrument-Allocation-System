<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== INVENTORY VERIFICATION ===\n\n";

// Check total records
$totalInstruments = DB::table('instruments')->count();
echo "Total Instruments: $totalInstruments\n\n";

// Check by department
$departments = ['ICT', 'IAT', 'ET', 'AT'];
foreach ($departments as $index => $deptName) {
    $deptId = $index + 1;
    $count = DB::table('instruments')->where('department_id', $deptId)->count();
    $totalQty = DB::table('instruments')->where('department_id', $deptId)->sum('total_quantity');
    echo "$deptName Department: $count items, Total Quantity: $totalQty\n";
}

echo "\n=== Sample Items with Quantities ===\n\n";

// Show some items with quantity > 1
$multipleQtyItems = DB::table('instruments')
    ->where('total_quantity', '>', 1)
    ->select('instrument_name', 'department_id', 'total_quantity', 'available_quantity')
    ->orderBy('total_quantity', 'desc')
    ->take(10)
    ->get();

foreach ($multipleQtyItems as $item) {
    $dept = $departments[$item->department_id - 1];
    echo "[$dept] {$item->instrument_name}: Total={$item->total_quantity}, Available={$item->available_quantity}\n";
}

echo "\n=== Verify No Duplicates ===\n\n";

// Check for duplicate instrument names in same department
$duplicates = DB::table('instruments')
    ->select('instrument_name', 'department_id', DB::raw('COUNT(*) as count'))
    ->groupBy('instrument_name', 'department_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    echo "WARNING: Found duplicates:\n";
    foreach ($duplicates as $dup) {
        echo "  {$dup->instrument_name} in dept {$dup->department_id}: {$dup->count} records\n";
    }
} else {
    echo "✓ No duplicates found - Each item has exactly ONE record\n";
}

echo "\n=== Verification Complete ===\n";
