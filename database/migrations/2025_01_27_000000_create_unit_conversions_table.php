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
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('to_unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('conversion_rate', 15, 2);
            $table->timestamps();
            
            // Prevent duplicate conversions for the same commodity and unit combination
            $table->unique(['commodity_id', 'from_unit_id', 'to_unit_id'], 'unique_commodity_unit_conversion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_conversions');
    }
}; 