<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCommoditiesTableRemoveSalesPriceAddProfitMargin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commodities', function (Blueprint $table) {
            // Remove sales_price column
            $table->dropColumn('sales_price');
            
            // Add profit_margin column for products
            $table->decimal('profit_margin', 5, 2)->nullable()->comment('Profit margin percentage for products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commodities', function (Blueprint $table) {
            // Add back sales_price column
            $table->double('sales_price')->nullable();
            
            // Remove profit_margin column
            $table->dropColumn('profit_margin');
        });
    }
}
