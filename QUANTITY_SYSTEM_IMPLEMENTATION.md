# Quantity-Based Inventory System - Implementation Summary

## ✅ Changes Completed

### Database Schema
- Added `total_quantity` and `available_quantity` to `instruments` table
- Added `quantity` (default: 1) to `bookings` table
- Added unique constraint on (`instrument_name`, `department_id`) to prevent duplicates
- Migration files: `2025_11_20_000010_add_quantity_and_unique_to_instruments_table.php` and `2025_11_20_000011_add_quantity_to_bookings_table.php`

### Models Enhanced

#### `Instruments.php`
- Added `total_quantity` and `available_quantity` to fillable
- **New Methods:**
  - `borrow($quantity)` - Reduces available_quantity when borrowing items
  - `returnItems($quantity)` - Increases available_quantity when returning items
  - `scopeAvailable()` - Query only instruments with available_quantity > 0
- **Safety:** Prevents over-borrowing and over-returning

#### `Booking.php`
- Added `quantity` to fillable (defaults to 1)
- **Automatic Lifecycle Tracking:**
  - When booking status → `approved`: Decrements available_quantity
  - When booking status → `completed/cancelled/rejected`: Increments available_quantity
- Uses database transactions and row-level locking for safety

### Seeder Rewritten

**`FullInventorySeeder.php`** now:
- Creates **ONE record per item** (no duplicates like "Microscope #1", "Microscope #2")
- Uses `total_quantity` for count (e.g., Microscope: total_quantity=2)
- Sets `available_quantity = total_quantity` initially
- Enforces unique constraint per department
- Clears all related tables safely with foreign key checks

### Data Verification ✓

**Current Database State:**
- **100 unique instruments** across 4 departments
- ICT: 43 items (571 total units)
- IAT: 20 items (82 total units)
- ET: 15 items (17 total units)
- AT: 22 items (31 total units)
- **No duplicates** - each item has exactly ONE record

**Examples:**
- Computer Chairs (Batch 2): `total_quantity=105, available_quantity=105`
- IoT Arduino starter kit: `total_quantity=20, available_quantity=20`
- Microscope: `total_quantity=2, available_quantity=2`

## Usage Examples

### 1. Direct Borrow/Return
```php
$instrument = Instruments::find(1);

// Borrow 3 units
$instrument->borrow(3);
// available_quantity reduced by 3

// Return 2 units
$instrument->returnItems(2);
// available_quantity increased by 2
```

### 2. Via Bookings (Automatic)
```php
$booking = Booking::create([
    'instrument_id' => 5,
    'quantity' => 2,  // Booking 2 units
    'status' => 'pending',
    // ... other fields
]);

// When approved
$booking->status = 'approved';
$booking->save();
// ✓ Automatically decrements available_quantity by 2

// When completed/cancelled
$booking->status = 'completed';
$booking->save();
// ✓ Automatically increments available_quantity by 2
```

### 3. Query Available Instruments
```php
// Get all instruments with available units
$available = Instruments::available()->get();

// Check specific instrument
if ($instrument->available_quantity > 0) {
    echo "Can book this instrument";
}
```

### 4. Safety Features
```php
// Prevents over-borrowing
try {
    $instrument->borrow(100);  // Only 20 available
} catch (\RuntimeException $e) {
    // "Not enough available units to borrow"
}

// Prevents over-returning
try {
    $instrument->returnItems(50);  // total_quantity is 20
} catch (\RuntimeException $e) {
    // "Return would exceed total quantity"
}
```

## Migration Commands

To apply this system:

```powershell
# Reset database with new schema and seed data
C:\xampp\php\php.exe artisan migrate:fresh --seed

# Or just run migrations (keeps existing data)
C:\xampp\php\php.exe artisan migrate

# Seed only
C:\xampp\php\php.exe artisan db:seed --class=FullInventorySeeder
```

## Testing

Verification scripts created:
- `verify_inventory.php` - Check data integrity and counts
- `test_quantity_tracking.php` - Test borrow/return logic

```powershell
C:\xampp\php\php.exe verify_inventory.php
C:\xampp\php\php.exe test_quantity_tracking.php
```

## Benefits

✅ **No Duplicate Records** - Each item stored once with quantity  
✅ **Automatic Tracking** - Booking lifecycle manages availability  
✅ **Data Integrity** - Transactional operations with row locking  
✅ **Safety Checks** - Prevents invalid borrow/return operations  
✅ **Scalable** - Handles items with high quantities (e.g., 105 chairs)  
✅ **Clear Semantics** - `total_quantity` vs `available_quantity`

## Future Enhancements

Consider adding:
- Booking queue when available_quantity reaches 0
- History tracking of quantity changes
- Low stock alerts (e.g., available < 20% of total)
- Reservation system for future dates
