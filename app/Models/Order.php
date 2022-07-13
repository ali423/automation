<?php

namespace App\Models;

use App\Services\BaseService;
use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, ActivityTrait, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'commodity_id',
        'commodity_amount',
        'unit',
        'deadline',
        'price',
        'status',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'commodity_id');
    }

    public function getKgAmountAttribute()
    {
          $obj=new BaseService();
          return $obj->calculateCommodityAmount($this->commodity_amount,$this->unit);
    }
}
