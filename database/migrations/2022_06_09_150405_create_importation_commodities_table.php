<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportationCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importation_commodities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained();
            $table->integer('amount');
            $table->string('file')->nullable();
            $table->enum('status',['awaiting_approval','approvaled','rejected','expired','done'])->default('awaiting_approval');
            $table->softDeletes();
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
        Schema::dropIfExists('importation_commodities');
    }
}
