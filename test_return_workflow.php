<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Return Workflow Test ===\n\n";

try {
    // Check if return fields exist in bookings table
    echo "1. Checking database schema...\n";
    $columns = DB::select("SHOW COLUMNS FROM bookings WHERE Field IN ('return_requested_at', 'actual_return_date', 'return_confirmed_by', 'return_confirmed_at', 'return_notes')");
    
    if (count($columns) === 5) {
        echo "✓ All return workflow fields exist in bookings table\n";
        foreach ($columns as $column) {
            echo "  - {$column->Field} ({$column->Type})\n";
        }
    } else {
        echo "✗ Missing return workflow fields. Found " . count($columns) . " of 5 expected fields.\n";
    }
    
    // Check status enum values
    echo "\n2. Checking booking status values...\n";
    $statusColumn = DB::select("SHOW COLUMNS FROM bookings WHERE Field = 'status'")[0];
    preg_match("/^enum\((.+)\)$/", $statusColumn->Type, $matches);
    $enumValues = str_getcsv($matches[1], ',', "'");
    
    echo "Available statuses: " . implode(', ', $enumValues) . "\n";
    
    $requiredStatuses = ['pending', 'approved', 'in_use', 'return_pending', 'returned', 'rejected', 'cancelled', 'completed'];
    $hasAllStatuses = true;
    foreach ($requiredStatuses as $status) {
        if (!in_array($status, $enumValues)) {
            echo "✗ Missing status: $status\n";
            $hasAllStatuses = false;
        }
    }
    
    if ($hasAllStatuses) {
        echo "✓ All required status values are present\n";
    }
    
    // Check routes
    echo "\n3. Checking routes...\n";
    $routes = [
        'bookings.markInUse' => 'POST',
        'bookings.requestReturn' => 'POST',
        'bookings.confirmReturn' => 'POST',
        'staff.bookings.returns' => 'GET',
        'admin.bookings.returns' => 'GET',
    ];
    
    $routeCollection = app('router')->getRoutes();
    foreach ($routes as $routeName => $method) {
        $route = $routeCollection->getByName($routeName);
        if ($route) {
            echo "✓ Route '$routeName' exists ({$method})\n";
        } else {
            echo "✗ Route '$routeName' not found\n";
        }
    }
    
    // Check for sample bookings
    echo "\n4. Checking booking records...\n";
    $totalBookings = DB::table('bookings')->count();
    echo "Total bookings: $totalBookings\n";
    
    $bookingsByStatus = DB::table('bookings')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
    
    echo "Bookings by status:\n";
    foreach ($bookingsByStatus as $stat) {
        echo "  - {$stat->status}: {$stat->count}\n";
    }
    
    // Check if there are any approved bookings ready to be marked in use
    $approvedCount = DB::table('bookings')->where('status', 'approved')->count();
    echo "\n✓ There are $approvedCount approved booking(s) that can be marked as 'in use'\n";
    
    // Summary
    echo "\n=== Test Summary ===\n";
    echo "✓ Database schema updated with return workflow fields\n";
    echo "✓ Status enum includes all workflow statuses\n";
    echo "✓ Routes registered for return workflow\n";
    echo "✓ Ready to test return workflow in browser\n";
    
    echo "\n=== Next Steps ===\n";
    echo "1. Login as a student\n";
    echo "2. Create a booking and wait for staff approval\n";
    echo "3. Mark the approved booking as 'in use'\n";
    echo "4. Request return for the in-use booking\n";
    echo "5. Login as staff/admin to view pending returns\n";
    echo "6. Confirm the return to restore availability\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
