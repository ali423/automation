<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the percentage column exists before trying to modify it
        if (Schema::hasColumn('product_formula', 'percentage')) {
            // Modify percentage column to allow NULL values using raw SQL
            DB::statement('ALTER TABLE product_formula MODIFY percentage DOUBLE NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the percentage column exists before trying to modify it
        if (Schema::hasColumn('product_formula', 'percentage')) {
            // Revert percentage column to not allow NULL values using raw SQL
            DB::statement('ALTER TABLE product_formula MODIFY percentage DOUBLE NOT NULL');
        }    }
};
