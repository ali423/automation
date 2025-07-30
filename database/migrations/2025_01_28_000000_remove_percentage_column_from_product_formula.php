<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_formula', function (Blueprint $table) {
            // Remove the percentage column completely
            $table->dropColumn('percentage');
        });

        // Make amount and unit_id required (not nullable) using raw SQL
        DB::statement('ALTER TABLE product_formula MODIFY amount DECIMAL(15,4) NOT NULL');
        DB::statement('ALTER TABLE product_formula MODIFY unit_id BIGINT UNSIGNED NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_formula', function (Blueprint $table) {
            // Add back the percentage column
            $table->double('percentage')->nullable();
        });

        // Make amount and unit_id nullable again using raw SQL
        DB::statement('ALTER TABLE product_formula MODIFY amount DECIMAL(15,4) NULL');
        DB::statement('ALTER TABLE product_formula MODIFY unit_id BIGINT UNSIGNED NULL');
    }
}; 