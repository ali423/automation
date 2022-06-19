<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportingCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importing_commodities', function (Blueprint $table) {
            $table->foreignId('importation_id')->references('id')->on('importing_requests')->onDelete('cascade');
            $table->foreignId('commodity_id')->constrained();
            $table->primary(['importation_id','commodity_id']);
            $table->integer('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('importing_commodities');
    }
}
