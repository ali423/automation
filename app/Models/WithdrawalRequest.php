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
        $boxQuantities = [];
        
        foreach ($this->commodities as $commodity) {
            $selectedUnitId = $commodity->pivot->unit_id;
            $amount = $commodity->pivot->amount;
            $piecesPerBox = $commodity->pieces_per_box ?? 1;
            
            // Convert to pieces first if needed
            $amountInPieces = $this->convertToPieces($commodity, $amount, $selectedUnitId);
            
            if ($amountInPieces !== null) {
                $boxes = floor($amountInPieces / $piecesPerBox);
                $remainingPieces = $amountInPieces % $piecesPerBox;
                
                $boxQuantities[$commodity->id] = [
                    'commodity_title' => $commodity->title,
                    'original_amount' => $amount,
                    'original_unit' => $commodity->pivot->unit,
                    'pieces_amount' => $amountInPieces,
                    'pieces_per_box' => $piecesPerBox,
                    'total_pieces' => $amountInPieces, // Total pieces for invoice
                    'boxes' => $boxes,
                    'remaining_pieces' => $remainingPieces,
                    'can_calculate' => true
                ];
            }
        }
        
        return $boxQuantities;
    }

    /**
     * Convert amount to pieces
     */
    private function convertToPieces($commodity, $amount, $unitId)
    {
        // If the selected unit is the main unit, we can use the amount directly
        // as the "pieces" equivalent for box calculations
        if ($unitId === $commodity->unit_id) {
            return $amount;
        }
        
        // Convert through main unit if possible
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        $amountInMainUnit = $commodityUnitService->convertToMainUnit($commodity, $amount, $unitId);
        
        if ($amountInMainUnit === null) {
            return null;
        }
        
        // For box calculations, we'll use the main unit amount as the "pieces" equivalent
        // This allows users to define their own pieces-per-box ratio regardless of the actual unit
        return $amountInMainUnit;
    }
}
