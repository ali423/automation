<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommodityWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_warehouse', function (Blueprint $table) {
            $table->foreignId('commodity_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->primary(['commodity_id','warehouse_id']);
            $table->integer('commodity_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commodity_warehouse');
    }
}
