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
        Schema::table('product_formula', function (Blueprint $table) {
            // Remove the percentage column completely
            $table->dropColumn('percentage');
            
            // Make amount and unit_id required (not nullable)
            $table->decimal('amount', 15, 4)->nullable(false)->change();
            $table->foreignId('unit_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_formula', function (Blueprint $table) {
            // Add back the percentage column
            $table->double('percentage')->nullable();
            
            // Make amount and unit_id nullable again
            $table->decimal('amount', 15, 4)->nullable()->change();
            $table->foreignId('unit_id')->nullable()->change();
        });
    }
}; 