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
            // Add new columns for unit-based amounts
            $table->decimal('amount', 15, 4)->nullable()->after('percentage');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('cascade')->after('amount');
            
            // Modify percentage column to allow NULL values for backward compatibility during transition
            $table->double('percentage')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_formula', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['amount', 'unit_id']);
        });
    }
}; 