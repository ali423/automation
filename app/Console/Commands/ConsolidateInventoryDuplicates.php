<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;

class ConsolidateInventoryDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:consolidate-duplicates 
                            {--dry-run : Preview changes without applying them}
                            {--force : Skip confirmation prompt (use with caution)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consolidate duplicate inventory records for the same commodity-unit combination';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        if ($isDryRun) {
            $this->info('🔍 Running in DRY-RUN mode - no changes will be made');
        } else {
            $this->warn('⚠️  Running in LIVE mode - duplicates will be merged');
            $this->warn('⚠️  This will modify production data!');
            
            if (!$force) {
                $this->newLine();
                $this->warn('IMPORTANT: Ensure you have:');
                $this->line('  1. Created a database backup');
                $this->line('  2. Tested on staging environment');
                $this->line('  3. Scheduled during low-traffic period');
                $this->newLine();
                
                if (!$this->confirm('Have you completed the above steps and want to proceed?', false)) {
                    $this->error('❌ Operation cancelled by user');
                    return Command::FAILURE;
                }
            }
        }

        // Find all duplicate combinations
        $duplicates = DB::table('inventories')
            ->select('commodity_id', 'unit_id', DB::raw('COUNT(*) as count'))
            ->groupBy('commodity_id', 'unit_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate inventory records found!');
            return Command::SUCCESS;
        }

        $this->warn("Found {$duplicates->count()} commodity-unit combinations with duplicates");
        
        $totalRecordsToMerge = 0;
        $totalRecordsToDelete = 0;

        foreach ($duplicates as $duplicate) {
            $records = Inventory::where('commodity_id', $duplicate->commodity_id)
                ->where('unit_id', $duplicate->unit_id)
                ->with(['commodity', 'unit'])
                ->orderBy('created_at', 'asc')
                ->get();

            if ($records->count() <= 1) {
                continue;
            }

            $commodity = $records->first()->commodity;
            $unit = $records->first()->unit;
            $commodityName = $commodity ? $commodity->title : "ID: {$duplicate->commodity_id}";
            $unitName = $unit ? $unit->name : "ID: {$duplicate->unit_id}";

            $this->line("\n📦 {$commodityName} ({$unitName}):");
            $this->line("   Found {$records->count()} duplicate records");

            // Calculate totals
            $totalAmount = 0;
            $weightedPriceSum = 0;
            $totalWeightForPrice = 0;
            $recordIds = [];

            foreach ($records as $index => $record) {
                $this->line("   [{$index}] ID: {$record->id} | Amount: {$record->amount} | Price: {$record->purchase_price} | Created: {$record->created_at}");
                $totalAmount += $record->amount;
                $recordIds[] = $record->id;
                
                if ($record->purchase_price !== null && $record->amount > 0) {
                    $weightedPriceSum += $record->purchase_price * $record->amount;
                    $totalWeightForPrice += $record->amount;
                }
            }

            $averagePurchasePrice = $totalWeightForPrice > 0 
                ? round($weightedPriceSum / $totalWeightForPrice, 2)
                : $records->first()->purchase_price;

            $this->info("   → Will merge into: Amount: {$totalAmount} | Avg Price: {$averagePurchasePrice}");
            
            $totalRecordsToMerge++;
            $totalRecordsToDelete += ($records->count() - 1);

            if (!$isDryRun) {
                DB::transaction(function () use ($records, $totalAmount, $averagePurchasePrice) {
                    // Update the first (oldest) record
                    $firstRecord = $records->first();
                    $firstRecord->update([
                        'amount' => $totalAmount,
                        'purchase_price' => $averagePurchasePrice,
                    ]);

                    // Delete the rest
                    $records->skip(1)->each(function ($record) {
                        $record->delete();
                    });
                });
                
                $this->info("   ✅ Merged successfully!");
            }
        }

        $this->newLine();
        
        if ($isDryRun) {
            $this->info("📊 Summary (Dry Run):");
            $this->line("   • {$totalRecordsToMerge} commodity-unit combinations have duplicates");
            $this->line("   • {$totalRecordsToDelete} duplicate records would be removed");
            $this->line("   • {$totalRecordsToMerge} records would be updated with merged data");
            $this->newLine();
            $this->warn("Run without --dry-run to apply changes");
        } else {
            $this->info("✅ Consolidation complete!");
            $this->line("   • Merged {$totalRecordsToMerge} commodity-unit combinations");
            $this->line("   • Removed {$totalRecordsToDelete} duplicate records");
        }

        return Command::SUCCESS;
    }
}
