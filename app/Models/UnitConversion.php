<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitConversion extends Model
{
    use HasFactory, ActivityTrait;

    protected $fillable = [
        'commodity_id',
        'from_unit_id',
        'to_unit_id',
        'conversion_rate',
    ];

    protected $casts = [
        'conversion_rate' => 'decimal:2',
    ];

    
    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    
    public function fromUnit()
    {
        return $this->belongsTo(Unit::class, 'from_unit_id');
    }

   
    public function toUnit()
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }

  
    public function getReverseRateAttribute()
    {
        return 1 / $this->conversion_rate;
    }
} 