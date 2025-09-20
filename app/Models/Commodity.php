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
        'product_identifier',
        'title',
        'profit_margin',
        'type',
        'purchase_price',
        'warning_limit',
        'unit_id',
        'pieces_per_box',
        'weight_per_unit',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function unitConversions()
    {
        return $this->hasMany(UnitConversion::class);
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

    /**
     * Calculate the total weight in kg for a given amount of this commodity
     *
     * @param float $amount The amount of the commodity
     * @param int|null $unitId The unit ID (if null, uses main unit)
     * @return float|null The total weight in kg
     */
    public function calculateWeight($amount, $unitId = null)
    {
        return calculate_weight($this, $amount, $unitId);
    }

    /**
     * Get the weight per unit in kg
     *
     * @return float|null
     */
    public function getWeightPerUnitKgAttribute()
    {
        return $this->weight_per_unit;
    }
}
