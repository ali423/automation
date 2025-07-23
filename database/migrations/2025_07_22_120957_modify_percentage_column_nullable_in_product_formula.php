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
            // Modify percentage column to allow NULL values
            $table->double('percentage')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_formula', function (Blueprint $table) {
            // Revert percentage column to not allow NULL values
            $table->double('percentage')->nullable(false)->change();
        });
    }
}; 