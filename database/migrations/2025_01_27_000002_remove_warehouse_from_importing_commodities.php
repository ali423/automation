<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('importing_commodities', function (Blueprint $table) {
            // Drop the warehouse foreign key and column if they exist
            if (Schema::hasColumn('importing_commodities', 'warehouses_id')) {
                // Try to drop foreign key constraint, ignore if it doesn't exist
                try {
                    $table->dropForeign(['warehouses_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                $table->dropColumn('warehouses_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('importing_commodities', function (Blueprint $table) {
            // Add back the warehouse column and foreign key
            $table->foreignId('warehouses_id')->constrained()->after('commodity_id');
        });
    }
}; 