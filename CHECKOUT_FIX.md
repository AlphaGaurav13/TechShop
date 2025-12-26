# Checkout Page Fix - Database Schema Update

## Problem
The checkout page was failing with error:
```
Fatal error: Uncaught mysqli_sql_exception: Unknown column 'address_line1' in 'field list'
```

## Root Cause
The `users` table was missing address-related columns that the checkout functionality requires.

## Solution Applied

### 1. **Database Schema Update**
Added the following columns to the `users` table:
- `address_line1` (VARCHAR 255)
- `address_line2` (VARCHAR 255)
- `city` (VARCHAR 100)
- `state` (VARCHAR 100)
- `postal_code` (VARCHAR 20)
- `country` (VARCHAR 100)

### 2. **Files Modified**

#### a. `mysql/init.sql` (NEW)
- Created migration script that will automatically run when Docker container starts
- Adds address columns using `ALTER TABLE IF NOT EXISTS` to prevent errors

#### b. `docker-compose.yml`
- Updated MySQL service to mount `init.sql` as init script
- Will execute on first container startup

#### c. `client/checkout.php`
- Made address column handling more robust
- Uses `SELECT *` then maps available columns safely
- Added try-catch around address updates
- Gracefully handles missing columns

#### d. `migrate.php` (NEW)
- Manual migration script for emergency fixes
- Run via: `http://localhost:8090/migrate.php`
- Checks if columns exist before adding them

## How to Apply the Fix

### Option 1: Docker Container (Recommended)
```bash
# Clean up and rebuild
docker-compose down -v
docker-compose up -d

# The init.sql will run automatically and add the columns
```

### Option 2: Manual Migration (If columns still missing)
1. Visit: `http://localhost:8090/migrate.php`
2. If successful, you'll see: `{"status":"success","message":"Address columns added successfully"}`

### Option 3: phpMyAdmin Direct SQL
1. Go to: `http://localhost:8083`
2. Login with root/root
3. Run the SQL from `mysql/init.sql` directly

## Testing
After applying the fix:
1. Login to the application
2. Add items to cart
3. Go to checkout
4. Address form should now work without SQL errors
5. Can save address and place order

## Backward Compatibility
- All address columns have DEFAULT NULL values
- Existing users will have NULL addresses initially
- No data loss for existing records
