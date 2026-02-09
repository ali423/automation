<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            
            // Adjustment type: withdrawal_approval, withdrawal_cancellation, sales_return, damage, etc.
            $table->enum('adjustment_type', [
                'withdrawal_approval',
                'withdrawal_cancellation',
                'sales_return',
                'inventory_damage',
                'manual_adjustment'
            ])->default('manual_adjustment');
            
            // Amount adjusted (positive = add, negative = remove)
            $table->decimal('amount', 15, 2);
            
            // Reason for adjustment
            $table->text('reason')->nullable();
            
            // Polymorphic relation to track which document triggered this adjustment
            $table->string('adjustable_type')->nullable(); // e.g., App\Models\WithdrawalRequest
            $table->unsignedBigInteger('adjustable_id')->nullable();
            
            // Who made the adjustment
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Financial tracking
            $table->decimal('value_before', 15, 2)->nullable(); // Stock value before adjustment
            $table->decimal('value_after', 15, 2)->nullable();  // Stock value after adjustment
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['commodity_id', 'created_at']);
            $table->index(['adjustable_type', 'adjustable_id']);
            $table->index('adjustment_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_adjustments');
    }
}
