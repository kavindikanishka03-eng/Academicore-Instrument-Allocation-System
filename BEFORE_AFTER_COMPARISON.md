# Before vs After: Inventory System Comparison

## ❌ OLD APPROACH (Duplicate Records)

### Database Structure
```
instruments table:
- Microscope #1 (instrument_code: ET-004-1)
- Microscope #2 (instrument_code: ET-004-2)
- Incubator #1 (instrument_code: ET-002-1)
- Incubator #2 (instrument_code: ET-002-2)
```

### Problems
- Multiple records for same item type
- Difficult to track total inventory
- Complex queries to count items
- No way to know "how many Microscopes total?"
- Booking system must pick specific unit (#1 or #2)

### Example Data
```php
id | name              | code       | status
---+-------------------+------------+-----------
15 | Microscope #1     | ET-004-1   | available
16 | Microscope #2     | ET-004-2   | in_use
```

---

## ✅ NEW APPROACH (Quantity-Based)

### Database Structure
```
instruments table:
- Microscope (total_quantity: 2, available_quantity: 2)
- Incubator (total_quantity: 2, available_quantity: 1)
```

### Benefits
- **ONE record per item type**
- Clear total vs available tracking
- Simple queries for availability
- Scales to large quantities (105 chairs → 1 record)
- Booking reserves "quantity" not specific unit

### Example Data
```php
id | name       | code   | total_qty | available_qty | status
---+------------+--------+-----------+---------------+-----------
4  | Microscope | ET-004 | 2         | 1             | available
2  | Incubator  | ET-002 | 2         | 1             | available
```

---

## Side-by-Side Comparison

### Scenario: Booking 1 Microscope

#### OLD WAY
1. Query: Find available Microscope (#1 or #2)
2. Create booking for specific unit (Microscope #1)
3. Update: `Microscope #1` status → 'in_use'
4. Problem: Must track which physical unit

#### NEW WAY
1. Query: Check `Microscope.available_quantity > 0`
2. Create booking with `quantity=1`
3. Automatic: `available_quantity` decrements to 1
4. System knows: 1 microscope available, 1 borrowed

### Scenario: 20 Arduino Kits

#### OLD WAY
```sql
INSERT INTO instruments VALUES
  ('IoT Arduino starter kit #1', 'IAT-005-1'),
  ('IoT Arduino starter kit #2', 'IAT-005-2'),
  ('IoT Arduino starter kit #3', 'IAT-005-3'),
  ... (17 more records) ...
  ('IoT Arduino starter kit #20', 'IAT-005-20');
-- Result: 20 database records
```

#### NEW WAY
```sql
INSERT INTO instruments VALUES
  ('IoT Arduino starter kit', 'IAT-005', 20, 20);
-- Result: 1 database record with total_quantity=20
```

### Scenario: Checking Availability

#### OLD WAY
```php
// Count available units
$count = Instruments::where('instrument_name', 'LIKE', 'Microscope%')
                    ->where('status', 'available')
                    ->count();
// Returns: 1 (only #1 available, #2 is in_use)
```

#### NEW WAY
```php
// Direct quantity check
$microscope = Instruments::where('instrument_name', 'Microscope')->first();
$available = $microscope->available_quantity;
// Returns: 1 (clear and instant)
```

---

## Database Size Impact

### OLD SYSTEM
- **701 records** for 100 unique items
  - 15 ET items → ~17 records
  - 20 IAT items → ~82 records  
  - 22 AT items → ~31 records
  - 43 ICT items → ~571 records (85 chairs + 105 chairs + 70 tables...)

### NEW SYSTEM
- **100 records** for 100 unique items
  - 15 ET items → 15 records
  - 20 IAT items → 20 records
  - 22 AT items → 22 records
  - 43 ICT items → 43 records

**Reduction: 85.7% fewer database records!**

---

## Migration Path

```powershell
# Backup existing data (if needed)
C:\xampp\php\php.exe artisan db:seed --class=BackupSeeder

# Apply new system
C:\xampp\php\php.exe artisan migrate:fresh --seed

# Verify
C:\xampp\php\php.exe verify_inventory.php
```

---

## Key Takeaways

| Aspect               | OLD (Duplicates)    | NEW (Quantities)     |
|---------------------|---------------------|----------------------|
| Records for 2 Microscopes | 2 rows         | 1 row                |
| Records for 105 Chairs    | 105 rows       | 1 row                |
| Database Size       | 701 records         | 100 records          |
| Query Complexity    | Complex (LIKE, COUNT) | Simple (column)    |
| Booking Logic       | Pick specific unit  | Reserve quantity     |
| Scalability         | Poor               | Excellent            |
| Inventory Clarity   | Fragmented         | Unified              |

**Conclusion:** The quantity-based approach is cleaner, more efficient, and scales better for items with multiple units.
