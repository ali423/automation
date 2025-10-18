# Production Migration: Profit Margin to Direct Sales Price

## Overview

This document outlines the migration from calculated pricing (profit_margin + base_cost) to direct pricing (sales_price) in the commodities system. The migration preserves all existing calculated prices while transitioning to a more efficient direct pricing model.

## Business Context

**Current System:**
- Products use `profit_margin` percentage (e.g., 20%)
- Sale price calculated on-the-fly: `base_cost × (1 + profit_margin/100)`
- Requires complex calculations for every price lookup

**Target System:**
- Products store direct `sales_price` values
- No runtime calculations needed
- Faster queries, simpler logic

## Migration Strategy

### Approach: Temp Database Snapshot

We use a temporary database snapshot to preserve existing `profit_margin` data while migrating to the new schema.

**Why this approach:**
- Zero data loss risk
- No need to rollback production migrations
- Can validate before applying changes
- Reversible if issues arise

## Pre-Migration Checklist

### 1. Database Backup
```bash
# Create full backup before any changes
mysqldump -u username -p database_name > backup_before_migration_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Create Temp Database Snapshot
```bash
# Create snapshot with profit_margin intact
mysqldump -u username -p database_name > temp_snapshot_$(date +%Y%m%d_%H%M%S).sql

# Create new database for snapshot
mysql -u username -p -e "CREATE DATABASE temp_migration_snapshot;"

# Import snapshot
mysql -u username -p temp_migration_snapshot < temp_snapshot_$(date +%Y%m%d_%H%M%S).sql
```

### 3. Configure Temp Database Connection
Add to `config/database.php`:

```php
'mysql_temp' => [
    'driver' => 'mysql',
    'host' => env('DB_TEMP_HOST', env('DB_HOST')),
    'port' => env('DB_TEMP_PORT', env('DB_PORT')),
    'database' => env('DB_TEMP_DATABASE', 'temp_migration_snapshot'),
    'username' => env('DB_TEMP_USERNAME', env('DB_USERNAME')),
    'password' => env('DB_TEMP_PASSWORD', env('DB_PASSWORD')),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
```

Add to `.env`:
```env
DB_TEMP_HOST=localhost
DB_TEMP_DATABASE=temp_migration_snapshot
DB_TEMP_USERNAME=your_username
DB_TEMP_PASSWORD=your_password
```

## Migration Commands

### 1. Validation Phase (Dry Run)

```bash
# Test connection to temp database
php artisan commodities:backfill-sales-price --source=mysql_temp --dry-run --chunk=100
```

**Expected Output:**
```
Source connection: mysql_temp
Chunk size: 100
Mode: DRY-RUN (no changes will be persisted)
[DRY] id=1 base=1000 margin=20.00% -> sales_price=1200
[DRY] id=2 base=2000 margin=15.00% -> sales_price=2300
...
Processed: 150
Updated:   145
Skipped:   5
```

**Validation Steps:**
1. Verify connection to temp database works
2. Check that sample calculations look correct
3. Note the counts (processed/updated/skipped)
4. Spot-check a few products manually

### 2. Production Execution

**During Maintenance Window:**

```bash
# Run the actual migration
php artisan commodities:backfill-sales-price --source=mysql_temp --chunk=500
```

**Expected Output:**
```
Source connection: mysql_temp
Chunk size: 500
Mode: LIVE
Processed: 150
Updated:   145
Skipped:   5
```

## Post-Migration Verification

### 1. Database Verification
```sql
-- Check that sales_price values were populated
SELECT id, title, sales_price, type 
FROM commodities 
WHERE type = 'product' 
AND sales_price IS NOT NULL 
LIMIT 10;

-- Verify no products have NULL sales_price
SELECT COUNT(*) as null_sales_price_count
FROM commodities 
WHERE type = 'product' 
AND sales_price IS NULL;
```

### 2. Application Verification
- Test product listing pages
- Verify order creation with new prices
- Check inventory management screens
- Test API endpoints that return prices

### 3. Spot Check Calculations
Pick 3-5 random products and manually verify:
```
Expected: sales_price = base_cost × (1 + profit_margin/100)
```

## Rollback Plan (If Needed)

### Emergency Rollback
If critical issues are discovered:

1. **Restore from backup:**
```bash
mysql -u username -p database_name < backup_before_migration_YYYYMMDD_HHMMSS.sql
```

2. **Revert code deployment** (if code changes were deployed)

### Partial Rollback
If only some products have issues:

```sql
-- Reset specific products to NULL (will use fallback logic)
UPDATE commodities 
SET sales_price = NULL 
WHERE id IN (problematic_product_ids);
```

## Cleanup (After Successful Migration)

### 1. Remove Temp Database
```bash
mysql -u username -p -e "DROP DATABASE temp_migration_snapshot;"
```

### 2. Remove Temp Connection
- Remove `mysql_temp` connection from `config/database.php`
- Remove temp database credentials from `.env`

### 3. Archive Snapshots
- Move backup files to archive location
- Document migration completion

## Monitoring and Alerts

### Key Metrics to Monitor
- Product count with NULL sales_price (should be 0 after migration)
- Order creation success rate
- API response times for price-related endpoints
- Error logs for pricing-related issues

### Alert Conditions
- Any product with NULL sales_price after migration
- Significant increase in pricing-related errors
- Order creation failures

## Troubleshooting

### Common Issues

**1. Connection to temp database fails**
- Verify temp database exists and is accessible
- Check credentials in `.env`
- Ensure temp database has `profit_margin` column

**2. High number of skipped records**
- Check if product formulas are complete
- Verify unit conversions are set up correctly
- Review logs for specific skip reasons

**3. Incorrect calculated prices**
- Verify base cost calculations in temp database
- Check that profit_margin values are correct
- Ensure unit conversions are consistent

### Debug Commands
```bash
# Check specific product calculation
php artisan tinker
>>> $product = App\Models\Commodity::find(123);
>>> $baseCost = app(App\Services\ProductFormulaService::class)->calculateMaterialCost($product);
>>> $salesPrice = $baseCost * (1 + 20/100);
>>> echo "Base: $baseCost, Sales: $salesPrice";
```

## Timeline Estimate

- **Preparation:** 30 minutes
- **Validation:** 15 minutes  
- **Execution:** 10-30 minutes (depending on data size)
- **Verification:** 30 minutes
- **Cleanup:** 15 minutes

**Total:** 1.5-2 hours

## Success Criteria

- [ ] All products have valid sales_price values
- [ ] No increase in application errors
- [ ] Order creation works normally
- [ ] API responses include correct prices
- [ ] UI displays prices correctly
- [ ] Performance is maintained or improved

## Contact Information

**Migration Lead:** [Your Name]  
**Backup Contact:** [Senior Developer]  
**Database Admin:** [DBA Contact]

---

**Document Version:** 1.0  
**Last Updated:** [Current Date]  
**Next Review:** [Date + 1 month]
