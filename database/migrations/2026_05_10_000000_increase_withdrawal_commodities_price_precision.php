<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class IncreaseWithdrawalCommoditiesPricePrecision extends Migration
{
    /**
     * Increase stored unit price precision for withdrawal lines (Tejarat invoice فـی).
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE withdrawal_commodities MODIFY price DECIMAL(18,5) NULL');
    }

    /**
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE withdrawal_commodities MODIFY price DECIMAL(10,2) NULL');
    }
}
