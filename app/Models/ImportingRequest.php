<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use App\Traits\CommentTrait;
use App\Traits\FileTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportingRequest extends Model
{
    use HasFactory,SoftDeletes,ActivityTrait,FileTrait,CommentTrait;
    protected $fillable = [
        'seller_id',
        'status',
        'number',
    ];
    
    protected $appends = ['main_unit_amount'];
    
    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'importing_commodities', 'importation_id', 'commodity_id')
            ->withPivot('amount','unit_id','purchase_price')
            ->with('unit');
    }
    public function getCreatedDateAttribute() {
        return  $this->created_at->format('Y-m-d');
    }

    public function seller() : BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
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
            
            $mainUnitAmounts[$commodity->id] = [
                'commodity_title' => $commodity->title,
                'original_amount' => $commodity->pivot->amount,
                'original_unit_id' => $selectedUnitId,
                'main_unit_amount' => $amountInMainUnit,
                'main_unit_name' => $commodity->unit ? $commodity->unit->name : 'نامشخص',
                'main_unit_symbol' => $commodity->unit ? $commodity->unit->symbol : '',
            ];
        }
        
        return $mainUnitAmounts;
    }
    

}
