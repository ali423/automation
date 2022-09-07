<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportingRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained();
            $table->enum('status', ['awaiting_approval', 'approvaled', 'rejected', 'expired', 'done'])->default('awaiting_approval');
            $table->bigInteger('number')->unique();
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
        Schema::dropIfExists('importing_requests');
    }
}
