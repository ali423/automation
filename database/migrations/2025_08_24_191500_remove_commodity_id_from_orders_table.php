<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCommodityIdFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['commodity_id']);
            // Then drop the column
            $table->dropColumn(['commodity_id', 'commodity_amount', 'unit']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add back the columns
            $table->foreignId('commodity_id')->constrained();
            $table->double('commodity_amount');
            $table->enum('unit', ['kg', 'keg', 'twenty_liters']);
        });
    }
}
