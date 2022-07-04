<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use App\Traits\CommentTrait;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
            ->withPivot('amount','warehouses_id','unit','price');
    }
    public function role()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function getTotalPriceAttribute(){
        $prices=array_column(array_column($this->commodities()->get()->toArray(),'pivot'),'price');
        if (in_array(null,$prices)){
            return null;
        }else{
            return array_sum($prices);
        }
    }
}
