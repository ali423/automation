<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactorWithdrawalCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the old table
        Schema::dropIfExists('withdrawing_commodities');
        
        // Create the new table following the same pattern as importing_commodities
        Schema::create('withdrawal_commodities', function (Blueprint $table) {
            $table->foreignId('withdrawal_id')->references('id')->on('withdrawal_requests')->onDelete('cascade');
            $table->foreignId('commodity_id')->constrained();
            $table->foreignId('unit_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->decimal('price', 10, 2)->nullable();
            $table->primary(['withdrawal_id', 'commodity_id']);
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
        
        // Recreate the old table structure
        Schema::create('withdrawing_commodities', function (Blueprint $table) {
            $table->foreignId('withdrawal_id')->references('id')->on('withdrawal_requests')->onDelete('cascade');
            $table->foreignId('commodity_id')->constrained();
            $table->enum('unit',['kg','keg','twenty_liters']);
            $table->primary(['withdrawal_id','commodity_id']);
            $table->json('amount');
            $table->double('price')->nullable();
        });
    }
} 