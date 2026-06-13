<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCancelledStatusToProductionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE production_requests MODIFY COLUMN status ENUM('awaiting_approval', 'approved', 'rejected', 'done', 'cancelled') DEFAULT 'awaiting_approval'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE production_requests MODIFY COLUMN status ENUM('awaiting_approval', 'approved', 'rejected', 'done') DEFAULT 'awaiting_approval'");
    }
}
