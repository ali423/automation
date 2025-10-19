<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Commodity;
use App\Services\ProductFormulaService;

class ModifyCommoditiesTableReplaceProfitMarginWithSalesPrice extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration safely replaces profit_margin with sales_price using a snapshot table approach:
     * 1. Create snapshot table with both columns
     * 2. Copy all data to snapshot
     * 3. Calculate sales_price in snapshot
     * 4. Alter original table (drop profit_margin, add sales_price)
     * 5. Copy calculated sales_price back
     * 6. Keep snapshot table as backup (contains original data with profit_margin)
     *
     * Note: The snapshot table 'commodities_snapshot' will be preserved after migration
     * as a safety backup. You can manually drop it later when you're confident.
     *
     * @return void
     */
    public function up()
    {
        echo "\n🚀 Starting safe migration: profit_margin → sales_price\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        try {
            // Step 1: Create snapshot table
            echo "Step 1/6: Creating snapshot table...\n";
            $this->createSnapshotTable();
            echo "✅ Snapshot table created\n\n";

            // Step 2: Copy data to snapshot
            echo "Step 2/6: Copying data to snapshot table...\n";
            $copiedCount = $this->copyDataToSnapshot();
            echo "✅ Copied {$copiedCount} records\n\n";

            // Step 3: Calculate sales_price in snapshot
            echo "Step 3/6: Calculating sales_price...\n";
            $calculatedCount = $this->calculateSalesPriceInSnapshot();
            echo "✅ Calculated sales_price for {$calculatedCount} products\n\n";

            // Step 4: Alter commodities table
            echo "Step 4/6: Altering commodities table...\n";
            $this->alterCommoditiesTable();
            echo "✅ Table structure updated\n\n";

            // Step 5: Copy sales_price back
            echo "Step 5/6: Copying sales_price back to commodities...\n";
            $updatedCount = $this->copySalesPriceBack();
            echo "✅ Updated {$updatedCount} records\n\n";

            // Step 6: Keep snapshot as backup
            echo "Step 6/6: Preserving snapshot table as backup...\n";
            $snapshotCount = DB::table('commodities_snapshot')->count();
            echo "✅ Snapshot table 'commodities_snapshot' kept with {$snapshotCount} records\n";
            echo "   💾 This table contains your original data including profit_margin\n";
            echo "   ℹ️  You can drop it later with: DROP TABLE commodities_snapshot;\n\n";

            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "🎉 Migration completed successfully!\n";
            echo "📦 Backup table 'commodities_snapshot' preserved for safety\n\n";
        } catch (\Throwable $e) {
            echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
            echo "Rolling back and cleaning up...\n";

            // Cleanup snapshot table on error
            try {
                DB::statement('DROP TABLE IF EXISTS commodities_snapshot');
            } catch (\Throwable $cleanupError) {
                // Ignore cleanup errors
            }

            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: If you have the snapshot table still available, you can restore 
     * profit_margin values from it before running this rollback.
     *
     * @return void
     */
    public function down()
    {
        echo "\n⚠️  Reverting migration: sales_price → profit_margin\n";
        echo "Note: Calculated sales_price values will be lost!\n";

        // Check if snapshot table exists
        $snapshotExists = DB::select("SHOW TABLES LIKE 'commodities_snapshot'");
        if (!empty($snapshotExists)) {
            echo "ℹ️  Snapshot table exists - original profit_margin data is preserved there\n";
            echo "   You can restore profit_margin values from commodities_snapshot if needed\n\n";
        } else {
            echo "⚠️  Snapshot table not found - profit_margin values cannot be restored\n\n";
        }

        Schema::table('commodities', function (Blueprint $table) {
            // Add back profit_margin column
            $table->decimal('profit_margin', 5, 2)->nullable()->comment('Profit margin percentage for products');

            // Remove sales_price column
            $table->dropColumn('sales_price');
        });

        echo "✅ Reverted successfully\n";

        if (!empty($snapshotExists)) {
            echo "\n💡 To restore profit_margin values from snapshot:\n";
            echo "   UPDATE commodities c INNER JOIN commodities_snapshot s ON c.id = s.id SET c.profit_margin = s.profit_margin;\n\n";
        }
    }

    /**
     * Create snapshot table with same structure as commodities
     */
    private function createSnapshotTable(): void
    {
        DB::statement('DROP TABLE IF EXISTS commodities_snapshot');
        DB::statement('CREATE TABLE commodities_snapshot LIKE commodities');
    }

    /**
     * Copy all data from commodities to snapshot table, then add sales_price column
     */
    private function copyDataToSnapshot(): int
    {
        // First, copy all data (same structure)
        DB::statement('INSERT INTO commodities_snapshot SELECT * FROM commodities');

        $count = DB::table('commodities_snapshot')->count();

        // Now add sales_price column to snapshot
        $hasColumn = Schema::hasColumn('commodities_snapshot', 'sales_price');
        if (!$hasColumn) {
            DB::statement("ALTER TABLE commodities_snapshot ADD COLUMN sales_price DECIMAL(10, 2) NULL COMMENT 'Calculated sales price'");
        }

        return $count;
    }

    /**
     * Calculate sales_price for all products in snapshot table
     */
    private function calculateSalesPriceInSnapshot(): int
    {
        $productFormulaService = app(ProductFormulaService::class);
        $updated = 0;
        $skipped = 0;

        // Get all products with profit_margin from snapshot
        $products = DB::table('commodities_snapshot')
            ->where('type', 'product')
            ->whereNotNull('profit_margin')
            ->select('id', 'profit_margin')
            ->get();

        $total = $products->count();
        echo "   Found {$total} products to process\n";

        foreach ($products as $product) {
            // Get the actual Commodity model with relations for cost calculation
            $commodity = Commodity::with(['materials', 'materials.unit', 'unit'])
                ->find($product->id);

            if (!$commodity) {
                $skipped++;
                continue;
            }

            // Calculate base cost
            $baseCost = (float) $productFormulaService->calculateMaterialCost($commodity);

            if ($baseCost <= 0) {
                $skipped++;
                continue;
            }

            // Calculate sales price: baseCost * (1 + profit_margin/100)
            $salesPrice = round($baseCost * (1 + ((float) $product->profit_margin / 100)), 2);

            // Update snapshot table with calculated sales_price
            DB::statement('UPDATE commodities_snapshot SET sales_price = ? WHERE id = ?', [$salesPrice, $product->id]);

            $updated++;

            // Progress indicator every 50 items
            if ($updated % 50 == 0) {
                echo "   Processed {$updated}/{$total}...\n";
            }
        }

        if ($skipped > 0) {
            echo "   ⚠️  Skipped {$skipped} products (no cost/margin data)\n";
        }

        return $updated;
    }

    /**
     * Alter commodities table: drop profit_margin, add sales_price
     */
    private function alterCommoditiesTable(): void
    {
        // Drop profit_margin column
        DB::statement('ALTER TABLE commodities DROP COLUMN profit_margin');

        // Add sales_price column
        DB::statement("ALTER TABLE commodities ADD COLUMN sales_price DECIMAL(10, 2) NULL COMMENT 'Direct sales price for products'");
    }

    /**
     * Copy sales_price from snapshot back to commodities table
     */
    private function copySalesPriceBack(): int
    {
        DB::statement('UPDATE commodities c INNER JOIN commodities_snapshot s ON c.id = s.id SET c.sales_price = s.sales_price WHERE s.sales_price IS NOT NULL');

        return DB::table('commodities')->whereNotNull('sales_price')->count();
    }

    /**
     * Drop the snapshot table (not used by default - kept for manual cleanup)
     * 
     * The snapshot table is preserved by default as a backup.
     * If you want to drop it manually later, you can run:
     * DROP TABLE IF EXISTS commodities_snapshot;
     */
    private function cleanupSnapshot(): void
    {
        DB::statement('DROP TABLE IF EXISTS commodities_snapshot');
        echo "✅ Snapshot table dropped\n";
    }
}
