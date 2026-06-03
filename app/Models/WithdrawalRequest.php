<?php

namespace App\Models;

use App\Traits\CommentTrait;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberToWords\NumberToWords;

class WithdrawalRequest extends Model
{
    use HasFactory, SoftDeletes, FileTrait, CommentTrait;
    
    protected $fillable = [
        'customer_id',
        'status',
        'number',
        'driver_name',
        'driver_phone',
        'driver_national_id',
        'vehicle_type',
        'plate_serial',
        'plate_number',
        'bill_of_lading_number',
        'shipping_city',
        'shipping_province',
    ];
    
    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'withdrawal_commodities', 'withdrawal_id', 'commodity_id')
            ->using(WithdrawalCommodity::class)
            ->withPivot('amount', 'unit_id', 'price', 'discount_percentage')
            ->with('unit');
    }
    
    /**
     * Get commodities with their pivot units properly loaded
     * This relationship ensures pivot units are eager loaded to avoid N+1 queries
     */
    public function commoditiesWithPivotUnits()
    {
        return $this->belongsToMany(Commodity::class, 'withdrawal_commodities', 'withdrawal_id', 'commodity_id')
            ->using(WithdrawalCommodity::class)
            ->withPivot('amount', 'unit_id', 'price', 'discount_percentage')
            ->with('unit');
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the related order
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'withdrawal_request_id', 'id');
    }

    /**
     * Get all inventory adjustments related to this withdrawal request
     * (corrections, returns, etc.)
     */
    public function adjustments()
    {
        return $this->morphMany(InventoryAdjustment::class, 'adjustable')
            ->orderBy('created_at', 'desc');
    }
    
    public function getCreatedDateAttribute() {
        return $this->created_at->format('Y-m-d');
    }
    
    /**
     * Get the amount in main unit for each commodity
     * This is a computed attribute that calculates the equivalent amount in the main unit
     */
    public function getMainUnitAmountAttribute()
    {
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        $mainUnitAmounts = [];
        
        foreach ($this->commodities as $commodity) {
            $selectedUnitId = $commodity->pivot->unit_id;
            $amountInMainUnit = $commodityUnitService->convertToMainUnit(
                $commodity,
                $commodity->pivot->amount,
                $selectedUnitId
            );
            
            // Ensure we always return a valid array structure, even if conversion fails
            $mainUnitAmounts[$commodity->id] = [
                'commodity_title' => $commodity->title,
                'original_amount' => $commodity->pivot->amount,
                'original_unit_id' => $selectedUnitId,
                'main_unit_amount' => $amountInMainUnit ?? 0, // Default to 0 if conversion fails
                'main_unit_name' => $commodity->unit ? $commodity->unit->name : 'نامشخص',
                'main_unit_symbol' => $commodity->unit ? $commodity->unit->symbol : '',
            ];
        }
        
        return $mainUnitAmounts;
    }
    
    /**
     * Override toArray method to exclude main_unit_amount from serialization
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Remove main_unit_amount from the array
        unset($array['main_unit_amount']);
        
        return $array;
    }
    
    public function getTotalPriceAttribute(){
        $prices = array_column(array_column($this->commodities()->get()->toArray(), 'pivot'), 'price');
        if (in_array(null, $prices)){
            return null;
        } else {
            $total_price = [];
            foreach ($this->commodities as $commodity){
                $total_price[] = round($commodity->pivot->price * $commodity->pivot->amount);
            }
            return [
                'number' => $res = array_sum($total_price),
                'world' => NumberToWords::transformNumber('fa', $res),
            ];
        }
    }

    /**
     * Get box quantities for each commodity based on user input
     */
    public function getBoxQuantitiesAttribute()
    {
        $boxCalculationService = app(\App\Services\BoxCalculationService::class);
        return $boxCalculationService->getWithdrawalBoxQuantities($this->commodities);
    }

    /**
     * Get the total weight in kg for all commodities in this withdrawal request
     */
    public function getTotalWeightKgAttribute()
    {
        return calculate_withdrawal_request_total_weight($this);
    }

    /**
     * Get weight breakdown for each commodity in the withdrawal request
     */
    public function getWeightBreakdownAttribute()
    {
        $breakdown = [];
        foreach ($this->commodities as $commodity) {
            $weight = calculate_weight($commodity, $commodity->pivot->amount, $commodity->pivot->unit_id);
            $breakdown[] = [
                'commodity_title' => $commodity->title,
                'amount' => $commodity->pivot->amount,
                'unit' => $commodity->unit ? $commodity->unit->symbol : 'نامشخص',
                'weight_kg' => $weight,
                'weight_per_unit_kg' => $commodity->weight_per_unit,
            ];
        }
        return $breakdown;
    }
}
