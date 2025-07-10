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
    use HasFactory, SoftDeletes, ActivityTrait, FileTrait, CommentTrait;
    
    protected $fillable = [
        'customer_id',
        'status',
        'number',
    ];
    
    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'withdrawal_commodities', 'withdrawal_id', 'commodity_id')
            ->withPivot('amount', 'unit_id', 'price')
            ->with('unit');
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
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
     * Override toArray method to exclude main_unit_amount from activity logging
     * This prevents issues with ActivityTrait when dealing with computed attributes
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Remove main_unit_amount from the array to prevent issues with ActivityTrait
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
}
