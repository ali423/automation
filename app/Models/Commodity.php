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
        'sales_price',
        'type',
        'purchase_price',
    ];

    public function Warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'commodity_warehouse', 'commodity_id', 'warehouse_id')
            ->withPivot('commodity_amount');
  }
    public function materials()
    {
        return $this->belongsToMany(Commodity::class, 'product_formula', 'product_id', 'material_id')
            ->withPivot('percentage')
            ->withTimestamps();
    }
    public function getBasePriceAttribute(){
        if ($this->type == 'product'){
            $total_amount=0;
            $materials=$this->materials()->get();
            foreach ($materials as $material){
                if ($material->type == 'material'){
                    $total_amount=$total_amount+round(($material->pivot->percentage/100) *$material->purchase_price,2);
                }else{
                    $total_amount=$total_amount+round(($material->pivot->percentage/100) *$material->base_price,2);
                }
            }
            return  $total_amount;
        }
        return $this->purchase_price;
    }
}
