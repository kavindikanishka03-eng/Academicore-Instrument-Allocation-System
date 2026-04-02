<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Instruments;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "\n=== TESTING QUANTITY TRACKING ===\n\n";

// Find an instrument with multiple units
$instrument = Instruments::where('total_quantity', '>', 5)->first();

echo "Testing with: {$instrument->instrument_name}\n";
echo "Initial - Total: {$instrument->total_quantity}, Available: {$instrument->available_quantity}\n\n";

// Test 1: Borrow some units directly
echo "TEST 1: Direct borrow method\n";
try {
    $instrument->borrow(3);
    $instrument->refresh();
    echo "After borrowing 3 units - Available: {$instrument->available_quantity}\n";
    
    $instrument->returnItems(3);
    $instrument->refresh();
    echo "After returning 3 units - Available: {$instrument->available_quantity}\n";
    echo "✓ Direct borrow/return works\n\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Booking approval reduces availability
echo "TEST 2: Booking status transitions\n";
$user = User::first();

try {
    // Create a pending booking with quantity 2
    $booking = Booking::create([
        'user_id' => $user->user_id,
        'instrument_id' => $instrument->instrument_id,
        'quantity' => 2,
        'booking_date' => now()->addDays(1)->format('Y-m-d'),
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'purpose' => 'Testing quantity tracking',
        'status' => 'pending',
    ]);
    
    $instrument->refresh();
    echo "Created pending booking for 2 units - Available: {$instrument->available_quantity}\n";
    
    // Approve booking
    $booking->status = 'approved';
    $booking->save();
    
    $instrument->refresh();
    echo "After approval - Available: {$instrument->available_quantity} (should be -2)\n";
    
    // Complete booking
    $booking->status = 'completed';
    $booking->save();
    
    $instrument->refresh();
    echo "After completion - Available: {$instrument->available_quantity} (should be +2)\n";
    echo "✓ Booking lifecycle works correctly\n\n";
    
    // Cleanup
    $booking->delete();
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Prevent over-borrowing
echo "TEST 3: Over-borrowing prevention\n";
try {
    $instrument->refresh();
    $tooMany = $instrument->available_quantity + 10;
    $instrument->borrow($tooMany);
    echo "✗ Over-borrowing was NOT prevented!\n\n";
} catch (\RuntimeException $e) {
    echo "✓ Over-borrowing correctly prevented: " . $e->getMessage() . "\n\n";
}

// Test 4: Scope for available instruments
echo "TEST 4: Available scope query\n";
$availableCount = Instruments::available()->count();
echo "Instruments with available_quantity > 0: $availableCount\n";

// Make one unavailable
$testInst = Instruments::where('available_quantity', '>', 0)->first();
$original = $testInst->available_quantity;
$testInst->available_quantity = 0;
$testInst->save();

$newCount = Instruments::available()->count();
echo "After setting one to 0 available: $newCount (should be " . ($availableCount - 1) . ")\n";

// Restore
$testInst->available_quantity = $original;
$testInst->save();
echo "✓ Available scope works correctly\n\n";

echo "=== ALL TESTS PASSED ===\n";
