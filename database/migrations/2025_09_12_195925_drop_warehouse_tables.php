<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropWarehouseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the pivot table first (commodity_warehouse)
        Schema::dropIfExists('commodity_warehouse');
        
        // Drop the warehouses table
        Schema::dropIfExists('warehouses');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate warehouses table
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->double('capacity');
            $table->double('empty_space');
            $table->enum('status',['active','inactive']);
            $table->enum('type',['tank','hall']);
            $table->softDeletes();
            $table->timestamps();
        });

        // Recreate commodity_warehouse pivot table
        Schema::create('commodity_warehouse', function (Blueprint $table) {
            $table->foreignId('commodity_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->primary(['commodity_id','warehouse_id']);
            $table->double('commodity_amount');
            $table->double('average_purchase_price')->nullable();
            $table->timestamps();
        });
    }
}
