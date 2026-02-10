<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCancelledStatusToWithdrawalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For MySQL, we need to use raw SQL to modify the enum
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN status ENUM('awaiting_approval', 'approvaled', 'rejected', 'expired', 'done', 'cancelled') DEFAULT 'awaiting_approval'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse the enum to its original values
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN status ENUM('awaiting_approval', 'approvaled', 'rejected', 'expired', 'done') DEFAULT 'awaiting_approval'");
    }
}
