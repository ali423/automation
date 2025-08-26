<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Unit;

class Commodity extends Model
{
    use HasFactory, ActivityTrait, SoftDeletes;

    protected $fillable = [
        'number',
        'title',
        'profit_margin',
        'type',
        'purchase_price',
        'warning_limit',
        'unit_id',
        'pieces_per_box',
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
            ->withPivot('amount', 'unit_id')
            ->withTimestamps();
    }

    public function importingRequests()
    {
        return $this->belongsToMany(ImportingRequest::class, 'importing_commodities', 'commodity_id', 'importation_id')
            ->withPivot('amount','unit_id','purchase_price');
    }

    public function getBasePriceAttribute()
    {
        if ($this->type == 'product') {
            $productFormulaService = app(\App\Services\ProductFormulaService::class);
            return $productFormulaService->calculateMaterialCost($this);
        }
        return $this->purchase_price;
    }

    public function getWithdrawalAmountAttribute()
    {
        $amounts = json_decode($this->pivot->amount) ??null;
        foreach ($amounts as $key => $value) {
            $res[] = [
                'warehouse' => Warehouse::query()->find($key),
                'amount' => $value,
                'unit' => $this->pivot->unit,
            ];
        }
        return $res ??null;
    }
    public function getTotalAmountAttribute(){
        $warehouses=$this->warehouses()->get()->toArray();
        $amounts=array_column(array_column($warehouses,'pivot'),'commodity_amount');
        return array_sum($amounts);
    }
    public function getAvrPriceAttribute(){
        $warehouses=$this->warehouses();
        if (!$warehouses->exists() || $this->type== 'product'){
            return null;
        }
        $numerator=0;
        $denominator=0;
        foreach ($warehouses->get() as $warehouse){
            $numerator=$numerator+($warehouse->pivot->commodity_amount*$warehouse->pivot->average_purchase_price);
            $denominator=$denominator+$warehouse->pivot->commodity_amount;
        }
        return round(($numerator/$denominator),2);
    }

    
    /**
     * Get all selectable units for this commodity
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSelectableUnitsAttribute()
    {
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        return $commodityUnitService->getSelectableUnits($this);
    }

    /**
     * Calculate the sales price dynamically based on material costs and profit margin
     *
     * @return float|null
     */
    public function getSalesPriceAttribute()
    {
        if ($this->type !== 'product') {
            return null;
        }

        $basePrice = $this->base_price;
        if ($basePrice === null || $this->profit_margin === null) {
            return null;
        }

        // Calculate sales price: base price + profit margin percentage
        $profitAmount = $basePrice * ($this->profit_margin / 100);
        return round($basePrice + $profitAmount, 2);
    }

    /**
     * Get the profit margin percentage
     *
     * @return float|null
     */
    public function getProfitMarginPercentageAttribute()
    {
        return $this->profit_margin;
    }
}
