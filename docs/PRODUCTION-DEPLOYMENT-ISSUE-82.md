# Production Deployment Guide - Issue #82 (Inventory Duplicates)

## ⚠️ CRITICAL - READ BEFORE DEPLOYING

This guide ensures safe deployment to production with large datasets.

---

## Pre-Deployment Checklist

### 1. **Backup Database** ✅
```bash
# Create full database backup
mysqldump -u username -p database_name > backup_before_issue82_$(date +%Y%m%d_%H%M%S).sql

# Or if using PostgreSQL
pg_dump -U username database_name > backup_before_issue82_$(date +%Y%m%d_%H%M%S).sql
```

### 2. **Test on Staging First** ✅
- Deploy all changes to staging environment
- Run consolidation command
- Verify no issues
- Test inventory operations (add, remove, search)
- Monitor performance

### 3. **Estimate Impact** ✅
```sql
-- Check how many duplicates exist
SELECT commodity_id, unit_id, COUNT(*) as duplicate_count
FROM inventories
GROUP BY commodity_id, unit_id
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC;

-- Total duplicate records
SELECT COUNT(*) as total_duplicates
FROM (
    SELECT commodity_id, unit_id
    FROM inventories
    GROUP BY commodity_id, unit_id
    HAVING COUNT(*) > 1
) as duplicates;

-- Estimate time: ~0.1-0.5 seconds per duplicate combination
```

---

## Safe Deployment Steps

### Phase 1: Code Deployment (Zero Downtime) ⏱️ 5 minutes

**During business hours - Safe to deploy**

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies (if any)
composer install --no-dev --optimize-autoloader

# 3. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Restart workers if using queue
php artisan queue:restart
```

**At this point:**
- ✅ New `addStock()` code with transaction locks is active
- ✅ No new duplicates will be created
- ✅ Existing duplicates still exist but system is stable

---

### Phase 2: Data Consolidation (Off-Peak Hours) ⏱️ 10-60 minutes

**Schedule during low-traffic period (e.g., 2 AM - 5 AM)**

#### Step 1: Preview Duplicates (Safe - Read-Only)
```bash
# See what will be merged WITHOUT making changes
php artisan inventory:consolidate-duplicates --dry-run > duplicates_report_$(date +%Y%m%d_%H%M%S).txt
```

**Review the output:**
- How many duplicates exist?
- Are the merged amounts reasonable?
- Any unexpected patterns?

#### Step 2: Create Second Backup (Before Data Changes)
```bash
# Backup just the inventories table
mysqldump -u username -p database_name inventories > inventories_backup_$(date +%Y%m%d_%H%M%S).sql
```

#### Step 3: Consolidate Duplicates (Data Modification)
```bash
# Run the actual consolidation
php artisan inventory:consolidate-duplicates

# Verify no duplicates remain
php artisan inventory:consolidate-duplicates --dry-run
```

**Expected output:** "✅ No duplicate inventory records found!"

#### Step 4: Verify Data Integrity
```sql
-- Verify no duplicates exist
SELECT commodity_id, unit_id, COUNT(*) as count
FROM inventories
GROUP BY commodity_id, unit_id
HAVING COUNT(*) > 1;
-- Should return 0 rows

-- Verify total amounts make sense (compare with backup)
SELECT 
    COUNT(*) as total_records,
    SUM(amount) as total_amount,
    AVG(amount) as avg_amount
FROM inventories;
```

---

### Phase 3: Add Unique Constraint ⏱️ 1-2 minutes

**After verifying consolidation is successful**

```bash
# Run the migration
php artisan migrate

# Verify constraint was added
php artisan db:show --table=inventories
```

---

## Performance Considerations

### Expected Timings (with 10,000+ inventory records)

| Operation | Time | Impact |
|-----------|------|--------|
| Code Deployment | 2-5 min | None - zero downtime |
| Consolidation (100 duplicates) | 5-10 min | Minimal - no table locks |
| Consolidation (1000 duplicates) | 30-60 min | Low traffic recommended |
| Adding Unique Constraint | <1 min | Brief table lock |

### Monitoring During Deployment

```bash
# Terminal 1: Monitor consolidation progress
php artisan inventory:consolidate-duplicates

# Terminal 2: Monitor database connections (MySQL)
watch -n 5 'mysql -e "SHOW PROCESSLIST;"'

# Terminal 3: Monitor application logs
tail -f storage/logs/laravel.log

# Terminal 4: Monitor system resources
htop
```

---

## Rollback Plan

### If Issues Occur During Consolidation

```bash
# Stop the consolidation (Ctrl+C)

# Restore from backup
mysql -u username -p database_name < inventories_backup_TIMESTAMP.sql

# Revert code changes
git checkout HEAD~1 app/Services/InventoryService.php
composer dump-autoload
php artisan cache:clear
```

### If Issues Occur After Migration

```bash
# Rollback the migration
php artisan migrate:rollback

# Restore database if needed
mysql -u username -p database_name < backup_before_issue82_TIMESTAMP.sql
```

---

## Post-Deployment Verification

### 1. Functional Testing ✅

```bash
# Test search functionality
# Search for "گالن چهار" in inventory management
# Verify only ONE record appears per commodity-unit combination

# Test adding stock
# Add stock to an existing commodity
# Verify no duplicates are created

# Test removing stock
# Remove stock from inventory
# Verify amount updates correctly
```

### 2. Performance Testing ✅

```sql
-- Check query performance
EXPLAIN SELECT * FROM inventories WHERE commodity_id = 1 AND unit_id = 1;
-- Should use the unique index

-- Verify index usage
SHOW INDEX FROM inventories;
-- Should show unique_commodity_unit index
```

### 3. Data Integrity Checks ✅

```sql
-- No duplicates exist
SELECT commodity_id, unit_id, COUNT(*)
FROM inventories
GROUP BY commodity_id, unit_id
HAVING COUNT(*) > 1;
-- Should return 0 rows

-- Amounts are reasonable
SELECT MIN(amount), MAX(amount), AVG(amount)
FROM inventories
WHERE amount > 0;

-- No negative amounts
SELECT COUNT(*) FROM inventories WHERE amount < 0;
-- Should be 0
```

---

## Risk Assessment

| Risk | Severity | Probability | Mitigation |
|------|----------|-------------|------------|
| Data loss during consolidation | High | Very Low | Full backup + dry-run testing |
| Long consolidation time | Medium | Medium | Run during off-peak hours |
| Migration timeout | Medium | Low | Consolidate BEFORE migration |
| Application downtime | Low | Very Low | Code deployment has zero downtime |
| Duplicate creation during consolidation | Low | Very Low | Transaction locks prevent this |
| Performance degradation | Low | Very Low | Unique index improves performance |

---

## Communication Plan

### Before Deployment
```
Subject: Scheduled Maintenance - Inventory System Optimization (Issue #82)

Dear Team,

We will perform a scheduled optimization of the inventory system:
- Date: [YYYY-MM-DD]
- Time: 02:00 AM - 05:00 AM
- Expected Impact: Minimal - system remains operational
- Purpose: Fix duplicate inventory records in search results

During this time:
✅ All inventory operations continue normally
✅ No data will be lost
✅ Search results will be cleaner and faster

Please report any issues to: [support contact]

Thank you!
```

### After Deployment
```
Subject: Completed - Inventory System Optimization (Issue #82)

The inventory system optimization is complete!

Results:
✅ Duplicate records merged successfully
✅ Search now shows one record per commodity
✅ No data loss
✅ System performance improved

Fixed Issue: Searching for commodities no longer shows multiple records with zero inventory.

If you notice any issues, please contact: [support contact]
```

---

## Troubleshooting

### Issue: Consolidation command times out

**Solution:**
```bash
# Increase PHP memory and timeout
php -d memory_limit=512M -d max_execution_time=3600 artisan inventory:consolidate-duplicates
```

### Issue: Migration fails with "Duplicate entry"

**Solution:**
```bash
# Re-run consolidation
php artisan inventory:consolidate-duplicates

# Verify no duplicates
php artisan inventory:consolidate-duplicates --dry-run

# Try migration again
php artisan migrate
```

### Issue: Performance degradation after deployment

**Solution:**
```bash
# Rebuild indexes
php artisan optimize

# Check for slow queries
# Enable MySQL slow query log and review
```

---

## Success Criteria

- ✅ Zero data loss
- ✅ No duplicate commodity-unit combinations in inventories table
- ✅ Unique constraint successfully added
- ✅ Search results show one record per commodity-unit
- ✅ No application downtime
- ✅ All inventory operations work correctly
- ✅ Response times remain acceptable (<200ms for search)

---

## Support Contacts

**If issues occur:**
1. Check application logs: `storage/logs/laravel.log`
2. Check consolidation report: `duplicates_report_*.txt`
3. Have backup timestamps ready for quick restore
4. Contact: [Your contact information]

---

## Quick Reference Commands

```bash
# Full deployment sequence
# 1. Backup
mysqldump -u user -p db > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Deploy code
git pull && composer install --no-dev --optimize-autoloader

# 3. Clear caches
php artisan cache:clear && php artisan config:clear

# 4. Preview duplicates
php artisan inventory:consolidate-duplicates --dry-run

# 5. Consolidate (during off-peak)
php artisan inventory:consolidate-duplicates

# 6. Verify
php artisan inventory:consolidate-duplicates --dry-run

# 7. Migrate
php artisan migrate

# 8. Test search in inventory management interface
```

---

**Last Updated:** January 3, 2026  
**Deployment Status:** Ready for Production  
**Risk Level:** LOW (with proper backup and testing)
