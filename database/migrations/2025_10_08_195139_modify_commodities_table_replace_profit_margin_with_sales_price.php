<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCommoditiesTableReplaceProfitMarginWithSalesPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commodities', function (Blueprint $table) {
            // Remove profit_margin column
            $table->dropColumn('profit_margin');
            
            // Add sales_price column for products
            $table->decimal('sales_price', 10, 2)->nullable()->comment('Direct sales price for products');
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
            // Add back profit_margin column
            $table->decimal('profit_margin', 5, 2)->nullable()->comment('Profit margin percentage for products');
            
            // Remove sales_price column
            $table->dropColumn('sales_price');
        });
    }
}
