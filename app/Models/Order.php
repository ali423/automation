<?php

namespace App\Models;

use App\Services\BaseService;
use App\Traits\ActivityTrait;
use Carbon\Carbon;
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
    public function getDeadlineDiffAttribute()
    {
      $shamsi_date=$this->deadline;
      $date_arr=explode('/',$shamsi_date);
        $month=$date_arr[1];
        $day=$date_arr[2];

        if (strlen($date_arr[1]) < 2){
          $month='0'.$date_arr[1];
      }
        if (strlen($date_arr[2]) < 2){
            $day='0'.$date_arr[2];
        }
      $new_date=$date_arr[0].'/'.$month.'/'.$day;
        $carbon_deadline = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $new_date)->toCarbon();
        $now_date=Carbon::now();
        return $carbon_deadline->diffInDays($now_date);
    }
}
