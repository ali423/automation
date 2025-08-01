<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductionCommodity extends Pivot
{
    protected $table = 'production_commodities';
    
    protected $fillable = [
        'production_request_id',
        'commodity_id',
        'type',
        'amount',
        'unit_id',
        'unit_cost',
        'total_cost',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];
    
    public function productionRequest()
    {
        return $this->belongsTo(ProductionRequest::class);
    }
    
    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }
    
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
} 