<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixWithdrawalCommoditiesPriceDecimalPrecision extends Migration
{
    /**
     * Use DECIMAL(28,5) so values from DECIMAL(25,2) stay in range (same integer capacity, more fractional digits).
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE withdrawal_commodities MODIFY price DECIMAL(28,5) NULL');
    }

    /**
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE withdrawal_commodities MODIFY price DECIMAL(25,2) NULL');
    }
}
