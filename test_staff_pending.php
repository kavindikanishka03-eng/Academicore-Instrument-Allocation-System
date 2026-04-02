<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Staff Pending Bookings Test ===\n\n";

try {
    // Get staff users
    echo "1. Checking staff users:\n";
    $staffUsers = DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.role_id')
        ->where('roles.role_name', 'staff')
        ->select('users.*', 'roles.role_name')
        ->get();
    
    if ($staffUsers->isEmpty()) {
        echo "✗ No staff users found!\n";
    } else {
        echo "✓ Found {$staffUsers->count()} staff user(s):\n";
        foreach ($staffUsers as $staff) {
            echo "  - {$staff->first_name} {$staff->last_name} (ID: {$staff->user_id}, Dept: {$staff->department_id})\n";
        }
    }
    
    // Get pending bookings
    echo "\n2. Checking pending bookings:\n";
    $pendingBookings = DB::table('bookings')
        ->join('instruments', 'bookings.instrument_id', '=', 'instruments.instrument_id')
        ->join('departments', 'instruments.department_id', '=', 'departments.department_id')
        ->join('users', 'bookings.user_id', '=', 'users.user_id')
        ->where('bookings.status', 'pending')
        ->select(
            'bookings.booking_id',
            'bookings.status',
            'instruments.instrument_name',
            'instruments.department_id',
            'departments.department_name',
            'users.first_name',
            'users.last_name'
        )
        ->get();
    
    if ($pendingBookings->isEmpty()) {
        echo "✗ No pending bookings found!\n";
    } else {
        echo "✓ Found {$pendingBookings->count()} pending booking(s):\n";
        foreach ($pendingBookings as $booking) {
            echo "  - Booking #{$booking->booking_id}: {$booking->instrument_name} ({$booking->department_name})\n";
            echo "    Student: {$booking->first_name} {$booking->last_name}\n";
        }
    }
    
    // Check if staff can see bookings from their department
    echo "\n3. Testing staff visibility:\n";
    foreach ($staffUsers as $staff) {
        $visibleBookings = DB::table('bookings')
            ->join('instruments', 'bookings.instrument_id', '=', 'instruments.instrument_id')
            ->where('bookings.status', 'pending')
            ->where('instruments.department_id', $staff->department_id)
            ->count();
        
        echo "  - {$staff->first_name} {$staff->last_name} (Dept {$staff->department_id}) can see: {$visibleBookings} booking(s)\n";
    }
    
    // Check role names case
    echo "\n4. Checking role names (case sensitivity):\n";
    $roles = DB::table('roles')->get();
    foreach ($roles as $role) {
        echo "  - Role ID {$role->role_id}: '{$role->role_name}'\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
