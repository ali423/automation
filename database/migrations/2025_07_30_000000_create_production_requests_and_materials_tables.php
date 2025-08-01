<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionRequestsAndMaterialsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create production_requests table
        Schema::create('production_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // The product to be produced
            $table->decimal('production_amount', 15, 4); // Amount to produce
            $table->unsignedBigInteger('unit_id'); // Unit of measurement
            $table->text('description')->nullable(); // Optional description
            $table->enum('status', ['awaiting_approval', 'approved', 'rejected', 'done'])->default('awaiting_approval');
            $table->string('number')->unique(); // Unique request number
            $table->decimal('total_cost', 15, 2)->default(0); // Total production cost
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('commodities')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });

        // Create production_materials pivot table
        Schema::create('production_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_request_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('required_amount', 15, 4); // Amount of material required
            $table->unsignedBigInteger('unit_id'); // Unit of the material
            $table->decimal('unit_cost', 15, 2)->default(0); // Cost per unit of material
            $table->decimal('total_cost', 15, 2)->default(0); // Total cost for this material
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('production_request_id')->references('id')->on('production_requests')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('commodities')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            // Unique constraint to prevent duplicate materials for same request
            $table->unique(['production_request_id', 'material_id'], 'unique_production_material');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_materials');
        Schema::dropIfExists('production_requests');
    }
} 