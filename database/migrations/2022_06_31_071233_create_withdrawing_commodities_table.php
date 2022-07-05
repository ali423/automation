<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawingCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawing_commodities', function (Blueprint $table) {
            $table->foreignId('withdrawal_id')->references('id')->on('withdrawal_requests')->onDelete('cascade');
            $table->foreignId('commodity_id')->constrained();
            $table->foreignId('warehouses_id')->constrained();
            $table->enum('unit',['kg','keg','twenty_liters']);
            $table->primary(['withdrawal_id','commodity_id']);
            $table->double('amount');
            $table->double('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawing_commodities');
    }
}
