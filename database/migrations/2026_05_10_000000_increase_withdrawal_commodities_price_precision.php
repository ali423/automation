<?php

use Illuminate\Database\Migrations\Migration;

class IncreaseWithdrawalCommoditiesPricePrecision extends Migration
{
    /**
     * Superseded by 2026_05_13_000000_fix_withdrawal_commodities_price_decimal_precision.
     * DECIMAL(18,5) was too narrow for DECIMAL(25,2) production data; keep migration record only.
     *
     * @return void
     */
    public function up()
    {
        //
    }

    /**
     * @return void
     */
    public function down()
    {
        //
    }
}
