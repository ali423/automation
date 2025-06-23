<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory;

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

    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class);
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

    public function getWithdrawalAmountAttribute()
    {
        $amounts = json_decode($this->pivot->amount) ?? null;
        foreach ($amounts as $key => $value) {
            $res[] = [
                'inventory' => Inventory::query()->find($key),
                'amount' => $value,
                'unit' => $this->pivot->unit,
            ];
        }
        return $res ?? null;
    }

    public function getTotalQuantityAttribute()
    {
        return $this->inventoryItems()->sum('quantity');
    }

    public function getAveragePurchasePriceAttribute()
    {
        if ($this->type == 'product') return null;
        
        $totalValue = 0;
        $totalQuantity = 0;
        
        foreach ($this->inventoryItems as $item) {
            $totalValue += ($item->quantity * $item->purchase_price);
            $totalQuantity += $item->quantity;
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
