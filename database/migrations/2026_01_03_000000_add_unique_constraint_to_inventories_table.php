<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueConstraintToInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * IMPORTANT: For production with large data:
     * 1. Run: php artisan inventory:consolidate-duplicates --dry-run
     * 2. Run: php artisan inventory:consolidate-duplicates
     * 3. Verify no duplicates exist before running this migration
     * 4. Then run: php artisan migrate
     *
     * @return void
     */
    public function up()
    {
        // Check if duplicates exist before proceeding
        $duplicateCount = DB::table('inventories')
            ->select('commodity_id', 'unit_id')
            ->groupBy('commodity_id', 'unit_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicateCount > 0) {
            throw new \Exception(
                "⚠️  Found {$duplicateCount} duplicate inventory records!\n" .
                "Please run these commands BEFORE running the migration:\n" .
                "1. php artisan inventory:consolidate-duplicates --dry-run\n" .
                "2. php artisan inventory:consolidate-duplicates\n" .
                "3. php artisan migrate\n" .
                "This prevents migration timeout and allows you to monitor the consolidation process."
            );
        }
        
        // Only add unique constraint if no duplicates exist
        Schema::table('inventories', function (Blueprint $table) {
            $table->unique(['commodity_id', 'unit_id'], 'unique_commodity_unit');
        });
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropUnique('unique_commodity_unit');
        });
    }
}
