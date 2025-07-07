<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Commodity extends Model
{
    use HasFactory, ActivityTrait, SoftDeletes;

    protected $fillable = [
        'number',
        'title',
        'sales_price',
        'type',
        'purchase_price',
        'warning_limit',
        'unit_id'
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function unitConversions()
    {
        return $this->hasMany(UnitConversion::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'commodity_warehouse', 'commodity_id', 'warehouse_id')
            ->withPivot('commodity_amount','average_purchase_price');
    }

    public function materials()
    {
        return $this->belongsToMany(Commodity::class, 'product_formula', 'product_id', 'material_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function importingRequests()
    {
        return $this->belongsToMany(ImportingRequest::class, 'importing_commodities', 'commodity_id', 'importation_id')
            ->withPivot('amount', 'unit', 'purchase_price');
    }

    public function getBasePriceAttribute()
    {
        if ($this->type == 'product') {
            $totalCost = 0;
            foreach ($this->productComponents as $component) {
                $requiredQty = $component->pivot->quantity;
                $totalCost += $requiredQty * $component->base_price;
            }
            return round($totalCost, 2);
        }
        return $this->purchase_price;
    }

    public function getTotalQuantityAttribute()
    {
        return $this->warehouses->sum('pivot.commodity_amount');
    }

    public function getAveragePurchasePriceAttribute()
    {
        if ($this->type == 'product') return null;
        
        $totalValue = 0;
        $totalQuantity = 0;
        
        foreach ($this->warehouses as $warehouse) {
            $totalValue += ($warehouse->pivot->commodity_amount * $warehouse->pivot->average_purchase_price);
            $totalQuantity += $warehouse->pivot->commodity_amount;
        }
        
        return $totalQuantity > 0 ? round($totalValue / $totalQuantity, 2) : null;
    }


    public function getKegAmountAttribute(){
        switch ($this->pivot->unit) {
            case 'keg':
                return $this->pivot->amount;
            case 'kg':
                return round($this->pivot->amount/185 , 1);
            case 'twenty_liters':
                return round(($this->pivot->amount*17.8)/185,1);
        }
    }
}
