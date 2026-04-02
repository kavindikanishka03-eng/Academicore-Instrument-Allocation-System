<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Instruments;
use Illuminate\Support\Facades\DB;

echo "\n=== TESTING AVAILABILITY DISPLAY ===\n\n";

// Find an instrument with multiple units
$instrument = Instruments::where('total_quantity', '>', 5)->first();

echo "Testing with: {$instrument->instrument_name}\n";
echo "Total: {$instrument->total_quantity}, Available: {$instrument->available_quantity}\n\n";

// Temporarily set available to 0
$originalAvail = $instrument->available_quantity;
$instrument->available_quantity = 0;
$instrument->save();

echo "✓ Set available_quantity to 0\n";
echo "  Status: {$instrument->status}\n";
echo "  Expected Display: 'All Units In Use' badge + disabled Quick Book button\n\n";

// Set to partial availability
$instrument->available_quantity = floor($instrument->total_quantity / 2);
$instrument->save();

echo "✓ Set available_quantity to {$instrument->available_quantity} (partial)\n";
echo "  Status: {$instrument->status}\n";
echo "  Expected Display: 'Available' badge + enabled Quick Book button\n";
echo "  Quantity shown: {$instrument->available_quantity} / {$instrument->total_quantity}\n\n";

// Restore original
$instrument->available_quantity = $originalAvail;
$instrument->save();

echo "✓ Restored original availability: {$originalAvail}\n\n";

echo "=== VIEW CHANGES ===\n";
echo "Card View:\n";
echo "  - Badge shows 'All Units In Use' when available_quantity = 0\n";
echo "  - Quick Book button disabled with 'All Units In Use' text\n";
echo "  - Progress bar shows usage percentage\n\n";

echo "Table View:\n";
echo "  - Quantity column shows: Available (green) / Total (blue)\n";
echo "  - Quick Book shows 'All In Use' when quantity = 0\n\n";

echo "Detail Page:\n";
echo "  - Status badge changes to 'All Units In Use' (orange)\n";
echo "  - Shows percentage available\n\n";

echo "Controller:\n";
echo "  - Prevents booking when available_quantity < 1\n";
echo "  - Error: 'All units of this instrument are currently in use'\n\n";

echo "=== TEST COMPLETE ===\n";
echo "Refresh browser to see changes!\n";
