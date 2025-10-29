<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseTotalCostPrecisionInProductionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify production_amount and total_cost in production_requests table
        Schema::table('production_requests', function (Blueprint $table) {
            $table->decimal('production_amount', 25, 4)->change();
            $table->decimal('total_cost', 25, 2)->default(0)->change();
        });

        // Modify required_amount, unit_cost and total_cost in production_materials table
        Schema::table('production_materials', function (Blueprint $table) {
            $table->decimal('required_amount', 25, 4)->change();
            $table->decimal('unit_cost', 25, 2)->default(0)->change();
            $table->decimal('total_cost', 25, 2)->default(0)->change();
        });

        // Modify amount and price in withdrawal_commodities table
        Schema::table('withdrawal_commodities', function (Blueprint $table) {
            $table->decimal('amount', 25, 4)->change();
            $table->decimal('price', 25, 2)->nullable()->change();
        });

        // Modify amount and purchase_price in importing_commodities table
        Schema::table('importing_commodities', function (Blueprint $table) {
            $table->decimal('amount', 25, 4)->change();
            $table->decimal('purchase_price', 25, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to original precision
        Schema::table('production_requests', function (Blueprint $table) {
            $table->decimal('production_amount', 15, 4)->change();
            $table->decimal('total_cost', 15, 2)->default(0)->change();
        });

        Schema::table('production_materials', function (Blueprint $table) {
            $table->decimal('required_amount', 15, 4)->change();
            $table->decimal('unit_cost', 15, 2)->default(0)->change();
            $table->decimal('total_cost', 15, 2)->default(0)->change();
        });

        Schema::table('withdrawal_commodities', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
            $table->decimal('price', 10, 2)->nullable()->change();
        });

        Schema::table('importing_commodities', function (Blueprint $table) {
            $table->double('amount')->change();
            $table->double('purchase_price')->nullable()->change();
        });
    }
}
