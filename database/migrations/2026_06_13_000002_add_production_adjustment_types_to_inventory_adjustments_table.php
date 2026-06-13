<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddProductionAdjustmentTypesToInventoryAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE inventory_adjustments MODIFY COLUMN adjustment_type ENUM(
            'withdrawal_approval',
            'withdrawal_cancellation',
            'sales_return',
            'inventory_damage',
            'manual_adjustment',
            'production_approval',
            'production_cancellation'
        ) DEFAULT 'manual_adjustment'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE inventory_adjustments MODIFY COLUMN adjustment_type ENUM(
            'withdrawal_approval',
            'withdrawal_cancellation',
            'sales_return',
            'inventory_damage',
            'manual_adjustment'
        ) DEFAULT 'manual_adjustment'");
    }
}
