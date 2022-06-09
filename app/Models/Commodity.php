<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commodity extends Model
{
    use HasFactory,ActivityTrait,SoftDeletes;

    protected $fillable = [
        'number',
        'title',
        'amount',
        'type',
    ];

    public function Warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'commodity_warehouse', 'commodity_id', 'warehouse_id')
            ->withPivot('commodity_amount')
            ->withTimestamps();
    }
}
