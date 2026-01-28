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
        // Update amount from decimal(15,4) to decimal(15,5)
        DB::statement('ALTER TABLE product_formula MODIFY amount DECIMAL(15,5) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert amount back to decimal(15,4)
        DB::statement('ALTER TABLE product_formula MODIFY amount DECIMAL(15,4) NOT NULL');
    }
};
