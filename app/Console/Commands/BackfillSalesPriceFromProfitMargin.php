<?php

namespace App\Console\Commands;

use App\Models\Commodity;
use App\Services\ProductFormulaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillSalesPriceFromProfitMargin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options:
     *  --source=          The source DB connection name (default: mysql)
     *  --chunk=           Chunk size for processing (default: 500)
     *  --dry-run          Do not persist changes, only simulate
     */
    protected $signature = 'commodities:backfill-sales-price 
                            {--source=mysql : Source DB connection containing profit_margin} 
                            {--chunk=500 : Chunk size} 
                            {--dry-run : Do not write changes}';

    /**
     * The console command description.
     */
    protected $description = 'Backfill commodities.sales_price using profit_margin from a source DB snapshot, computing base costs via product formula.';

    public function handle(ProductFormulaService $productFormulaService): int
    {
        $source = (string) $this->option('source');
        $chunkSize = (int) $this->option('chunk');
        $isDryRun = (bool) $this->option('dry-run');

        $this->info("Source connection: {$source}");
        $this->info("Chunk size: {$chunkSize}");
        $this->info($isDryRun ? 'Mode: DRY-RUN (no changes will be persisted)' : 'Mode: LIVE');

        // Ensure source connection is configured and responsive
        try {
            DB::connection($source)->select('select 1');
        } catch (\Throwable $e) {
            $this->error("Cannot connect to source connection '{$source}': " . $e->getMessage());
            return Command::FAILURE;
        }

        // Ensure source has profit_margin column before proceeding
        try {
            $hasColumn = \Illuminate\Support\Facades\Schema::connection($source)->hasColumn('commodities', 'profit_margin');
            if (!$hasColumn) {
                $this->error("Source connection '{$source}' does not have 'commodities.profit_margin'. Aborting.");
                return Command::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error("Unable to inspect source schema on '{$source}': " . $e->getMessage());
            return Command::FAILURE;
        }

        $totalProcessed = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        // We read products from the source DB where profit_margin is not null
        DB::connection($source)
            ->table('commodities')
            ->where('type', 'product')
            ->orderBy('id')
            ->select('id', 'profit_margin')
            ->chunkById($chunkSize, function ($rows) use (&$totalProcessed, &$totalUpdated, &$totalSkipped, $productFormulaService, $isDryRun) {
                // Preload all target commodities with relations for cost calc
                $ids = collect($rows)->pluck('id')->all();
                $targets = Commodity::with(['materials', 'materials.unit', 'unit'])
                    ->whereIn('id', $ids)
                    ->get()
                    ->keyBy('id');

                foreach ($rows as $row) {
                    $totalProcessed++;
                    $id = $row->id;
                    $profitMargin = $row->profit_margin; // may be null

                    /** @var Commodity|null $commodity */
                    $commodity = $targets->get($id);
                    if (!$commodity) {
                        $totalSkipped++;
                        $this->warn("Commodity id {$id} not found in target DB; skipping.");
                        continue;
                    }

                    // Compute base cost using current relations
                    $baseCost = (float) $productFormulaService->calculateMaterialCost($commodity);
                    if ($baseCost <= 0 || $profitMargin === null) {
                        $totalSkipped++;
                        $this->warn("Commodity id {$id} has baseCost={$baseCost} or missing profit_margin; skipping.");
                        continue;
                    }

                    $salesPrice = round($baseCost * (1 + ((float) $profitMargin / 100)), 2);

                    if ($isDryRun) {
                        $this->line("[DRY] id={$id} base={$baseCost} margin={$profitMargin}% -> sales_price={$salesPrice}");
                    } else {
                        $commodity->sales_price = $salesPrice;
                        $commodity->save();
                    }
                    $totalUpdated++;
                }
            });

        $this->info("Processed: {$totalProcessed}");
        $this->info("Updated:   {$totalUpdated}");
        $this->info("Skipped:   {$totalSkipped}");

        return Command::SUCCESS;
    }
}


