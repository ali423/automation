<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use App\Traits\CommentTrait;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberToWords\NumberToWords;

class WithdrawalRequest extends Model
{
    use HasFactory,SoftDeletes,ActivityTrait,FileTrait,CommentTrait;
    protected $fillable = [
        'customer_id',
        'status',
        'number',
    ];
    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'withdrawing_commodities', 'withdrawal_id', 'commodity_id')
            ->withPivot('amount','unit','price');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function getTotalPriceAttribute(){
        $prices=array_column(array_column($this->commodities()->get()->toArray(),'pivot'),'price');
        if (in_array(null,$prices)){
            return null;
        }else{
            foreach ($this->commodities as $commodity){
                $amount=array_sum(json_decode($commodity->pivot->amount,true));
                $total_price[]=round($commodity->pivot->price * $amount*10);
            }
            return[
                'number'=>$res=array_sum($total_price),
                'world'=>NumberToWords::transformNumber('fa', $res),
            ] ;
        }
    }
}
