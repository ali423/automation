<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_requests', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->enum('status', ['awaiting_approval', 'approvaled', 'rejected', 'expired', 'done'])->default('awaiting_approval');
            $table->bigInteger('number')->unique();
            $table->decimal('total_cost', 15, 2)->default(0);
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
        Schema::dropIfExists('production_requests');
    }
}
