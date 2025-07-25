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
        Schema::create('withdrawal_commodities', function (Blueprint $table) {
            $table->foreignId('withdrawal_id')->references('id')->on('withdrawal_requests')->onDelete('cascade');
            $table->foreignId('commodity_id')->constrained();
            $table->foreignId('unit_id')->constrained();
            $table->primary(['withdrawal_id','commodity_id']);
            $table->decimal('amount', 15, 2);
            $table->decimal('price', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawal_commodities');
    }
}
