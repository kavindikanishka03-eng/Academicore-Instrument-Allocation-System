<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Instruments;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "\n=== TESTING BOOKING APPROVAL WORKFLOW ===\n\n";

// Find test data
$instrument = Instruments::where('total_quantity', '>', 5)->first();
$student = User::whereHas('role', function($q) {
    $q->where('role_name', 'student');
})->first();

echo "Instrument: {$instrument->instrument_name}\n";
echo "Initial Availability: {$instrument->available_quantity} / {$instrument->total_quantity}\n\n";

// Test 1: Create booking as pending
echo "TEST 1: Create booking with 'pending' status\n";
try {
    $booking = Booking::create([
        'user_id' => $student->user_id,
        'instrument_id' => $instrument->instrument_id,
        'quantity' => 2,
        'booking_date' => now()->addDays(1)->format('Y-m-d'),
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'purpose' => 'Testing approval workflow',
        'status' => 'pending',
    ]);
    
    $instrument->refresh();
    echo "✓ Booking created with status: {$booking->status}\n";
    echo "  Available after creation: {$instrument->available_quantity} (should be unchanged)\n\n";
    
    // Test 2: Approve booking
    echo "TEST 2: Approve the booking\n";
    $booking->status = 'approved';
    $booking->approved_by = $student->user_id;
    $booking->approval_date = now();
    $booking->save();
    
    $instrument->refresh();
    echo "✓ Booking approved\n";
    echo "  Available after approval: {$instrument->available_quantity} (should be -2)\n\n";
    
    // Test 3: Complete booking
    echo "TEST 3: Complete the booking\n";
    $booking->status = 'completed';
    $booking->save();
    
    $instrument->refresh();
    echo "✓ Booking completed\n";
    echo "  Available after completion: {$instrument->available_quantity} (should be +2)\n\n";
    
    // Cleanup
    $booking->delete();
    echo "✓ Test booking cleaned up\n\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
    if (isset($booking) && $booking->exists) {
        $booking->delete();
    }
}

echo "=== WORKFLOW TEST COMPLETE ===\n";
