<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePiecesPerBoxFromWithdrawalCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawal_commodities', function (Blueprint $table) {
            $table->dropColumn('pieces_per_box');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawal_commodities', function (Blueprint $table) {
            $table->integer('pieces_per_box')->default(1)->after('price');
        });
    }
}
