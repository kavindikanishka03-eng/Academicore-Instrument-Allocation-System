<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating Test Booking ===\n\n";

try {
    // Get a student
    $student = DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.role_id')
        ->where('roles.role_name', 'student')
        ->first();
    
    if (!$student) {
        echo "✗ No student found!\n";
        exit;
    }
    
    echo "Student: {$student->first_name} {$student->last_name}\n";
    
    // Get an instrument from department 1 (Nimal's department)
    $instrument = DB::table('instruments')
        ->where('department_id', 1)
        ->where('available_quantity', '>', 0)
        ->first();
    
    if (!$instrument) {
        echo "✗ No available instruments in department 1!\n";
        exit;
    }
    
    echo "Instrument: {$instrument->instrument_name} (Dept 1)\n";
    
    // Create a pending booking
    $bookingId = DB::table('bookings')->insertGetId([
        'user_id' => $student->user_id,
        'instrument_id' => $instrument->instrument_id,
        'booking_date' => date('Y-m-d'),
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'quantity' => 1,
        'status' => 'pending',
        'purpose' => 'Testing staff visibility',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✓ Created booking #{$bookingId}\n\n";
    
    // Now check if staff can see it
    echo "Checking staff visibility:\n";
    $staffUser = DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.role_id')
        ->where('roles.role_name', 'staff')
        ->where('users.department_id', 1)
        ->first();
    
    if ($staffUser) {
        $count = DB::table('bookings')
            ->join('instruments', 'bookings.instrument_id', '=', 'instruments.instrument_id')
            ->where('bookings.status', 'pending')
            ->where('instruments.department_id', $staffUser->department_id)
            ->count();
        
        echo "✓ {$staffUser->first_name} {$staffUser->last_name} (Dept 1) can see: {$count} pending booking(s)\n";
    }
    
    echo "\n=== Test Complete ===\n";
    echo "Now login as Nimal Bandara (staff, Dept 1) and check /staff/bookings/pending\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
}
