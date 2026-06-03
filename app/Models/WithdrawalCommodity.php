<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WithdrawalCommodity extends Pivot
{
    public $incrementing = false;

    protected $table = 'withdrawal_commodities';

    protected $casts = [
        'withdrawal_id' => 'integer',
        'commodity_id' => 'integer',
        'unit_id' => 'integer',
        'amount' => 'decimal:2',
        'price' => 'decimal:5',
        'discount_percentage' => 'integer',
    ];
}
