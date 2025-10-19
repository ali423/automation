<?php

namespace App\Console\Commands;

use App\Models\Commodity;
use App\Services\ProductFormulaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SafeMigrateSalesPrice extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'commodities:safe-migrate-sales-price 
                            {--dry-run : Do not write changes}';

    /**
     * The console command description.
     */
    protected $description = 'Safely migrate from profit_margin to sales_price using snapshot table with transactions';

    public function handle(ProductFormulaService $productFormulaService): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info($isDryRun ? 'Mode: DRY-RUN (no changes will be persisted)' : 'Mode: LIVE');
        $this->info('Starting safe migration process...');

        try {
            DB::beginTransaction();

            // Step 1: Create snapshot table
            $this->info('Step 1: Creating snapshot table...');
            $this->createSnapshotTable();

            // Step 2: Copy data to snapshot
            $this->info('Step 2: Copying data to snapshot table...');
            $copiedCount = $this->copyDataToSnapshot();
            $this->info("Copied {$copiedCount} records to snapshot table");

            // Step 3: Calculate and fill sales_price in snapshot
            $this->info('Step 3: Calculating sales_price in snapshot table...');
            $calculatedCount = $this->calculateSalesPriceInSnapshot($productFormulaService);
            $this->info("Calculated sales_price for {$calculatedCount} records");

            // Step 4: Alter original commodities table
            $this->info('Step 4: Altering commodities table (drop profit_margin, add sales_price)...');
            if (!$isDryRun) {
                $this->alterCommoditiesTable();
                $this->info("Table altered successfully");
            } else {
                $this->line("[DRY-RUN] Would alter commodities table");
            }

            // Step 5: Copy sales_price back to commodities
            $this->info('Step 5: Copying sales_price back to commodities table...');
            $updatedCount = $this->copySalesPriceBackToCommodities();
            $this->info("Updated {$updatedCount} records in commodities table");

            // Step 6: Cleanup snapshot table
            $this->info('Step 6: Cleaning up snapshot table...');
            if (!$isDryRun) {
                $this->cleanupSnapshotTable();
                $this->info("Snapshot table dropped");
            } else {
                $this->line("[DRY-RUN] Would drop snapshot table");
            }

            if ($isDryRun) {
                $this->warn('DRY-RUN: Rolling back transaction...');
                DB::rollBack();
            } else {
                $this->info('Committing transaction...');
                DB::commit();
                $this->info('Migration completed successfully!');
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Migration failed! Transaction rolled back.');
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());

            // Try to cleanup snapshot table on error
            try {
                DB::statement('DROP TABLE IF EXISTS commodities_snapshot');
            } catch (\Throwable $cleanupError) {
                // Ignore cleanup errors
            }

            return Command::FAILURE;
        }
    }

    /**
     * Create snapshot table with both profit_margin and sales_price columns
     */
    private function createSnapshotTable(): void
    {
        // Drop if exists
        DB::statement('DROP TABLE IF EXISTS commodities_snapshot');

        // Create snapshot table with same structure
        DB::statement('CREATE TABLE commodities_snapshot LIKE commodities');

        // Check if sales_price column exists before adding
        $hasColumn = \Illuminate\Support\Facades\Schema::hasColumn('commodities_snapshot', 'sales_price');

        if (!$hasColumn) {
            // Add sales_price column
            DB::statement("ALTER TABLE commodities_snapshot ADD COLUMN sales_price DECIMAL(10, 2) NULL COMMENT 'Calculated sales price'");
        }
    }

    /**
     * Copy all data from commodities to snapshot table
     */
    private function copyDataToSnapshot(): int
    {
        DB::statement('INSERT INTO commodities_snapshot SELECT * FROM commodities');

        return DB::table('commodities_snapshot')->count();
    }

    /**
     * Calculate sales_price for all products in snapshot table
     */
    private function calculateSalesPriceInSnapshot(ProductFormulaService $productFormulaService): int
    {
        $updated = 0;
        $skipped = 0;

        // Get all products with profit_margin from snapshot
        $products = DB::table('commodities_snapshot')
            ->where('type', 'product')
            ->whereNotNull('profit_margin')
            ->select('id', 'profit_margin')
            ->get();

        $this->info("Found {$products->count()} products to process");

        foreach ($products as $product) {
            // Get the actual Commodity model with relations for cost calculation
            $commodity = Commodity::with(['materials', 'materials.unit', 'unit'])
                ->find($product->id);

            if (!$commodity) {
                $skipped++;
                $this->warn("Commodity id {$product->id} not found; skipping.");
                continue;
            }

            // Calculate base cost
            $baseCost = (float) $productFormulaService->calculateMaterialCost($commodity);

            if ($baseCost <= 0) {
                $skipped++;
                $this->warn("Commodity id {$product->id} has baseCost={$baseCost}; skipping.");
                continue;
            }

            // Calculate sales price: baseCost * (1 + profit_margin/100)
            $salesPrice = round($baseCost * (1 + ((float) $product->profit_margin / 100)), 2);

            // Update snapshot table with calculated sales_price
            DB::statement('UPDATE commodities_snapshot SET sales_price = ? WHERE id = ?', [$salesPrice, $product->id]);

            $updated++;

            if ($updated % 50 == 0) {
                $this->line("Processed {$updated} products...");
            }
        }

        if ($skipped > 0) {
            $this->warn("Skipped {$skipped} products due to missing data or zero cost");
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
    private function copySalesPriceBackToCommodities(): int
    {
        // Update commodities table with sales_price from snapshot
        DB::statement('UPDATE commodities c INNER JOIN commodities_snapshot s ON c.id = s.id SET c.sales_price = s.sales_price WHERE s.sales_price IS NOT NULL');

        // Count updated records
        $count = DB::table('commodities')->whereNotNull('sales_price')->count();

        return $count;
    }

    /**
     * Drop the snapshot table
     */
    private function cleanupSnapshotTable(): void
    {
        DB::statement('DROP TABLE IF EXISTS commodities_snapshot');
    }
}
