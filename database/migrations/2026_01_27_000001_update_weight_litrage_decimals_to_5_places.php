<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update weight_per_unit from decimal(10,3) to decimal(10,5)
        DB::statement('ALTER TABLE commodities MODIFY weight_per_unit DECIMAL(10,5) NULL');
        
        // Update litrage from decimal(10,2) to decimal(10,5)
        DB::statement('ALTER TABLE commodities MODIFY litrage DECIMAL(10,5) NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert weight_per_unit back to decimal(10,3)
        DB::statement('ALTER TABLE commodities MODIFY weight_per_unit DECIMAL(10,3) NULL');
        
        // Revert litrage back to decimal(10,2)
        DB::statement('ALTER TABLE commodities MODIFY litrage DECIMAL(10,2) NULL DEFAULT 0');
    }
};
