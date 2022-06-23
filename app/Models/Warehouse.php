<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory,SoftDeletes,ActivityTrait;

    protected $fillable = [
        'capacity',
        'title',
        'type',
        'status',
        'empty_space',
    ];
    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'commodity_warehouse', 'warehouse_id', 'commodity_id')
            ->withPivot('commodity_amount')
            ->withTimestamps();
    }
}
