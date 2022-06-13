<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFormulaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_formula', function (Blueprint $table) {
            $table->foreignId('product_id')->references('id')->on('commodities')->onDelete('cascade');
            $table->foreignId('material_id')->references('id')->on('commodities')->onDelete('cascade');
            $table->primary(['product_id','material_id']);
            $table->string('percentage');
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
        Schema::dropIfExists('product_formula');
    }
}
