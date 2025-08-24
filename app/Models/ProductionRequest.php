<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use App\Traits\CommentTrait;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionRequest extends Model
{
    use HasFactory, SoftDeletes, ActivityTrait, FileTrait, CommentTrait;
    
    protected $fillable = [
        'product_id',
        'production_amount',
        'unit_id',
        'description',
        'status',
        'number',
        'total_cost',
    ];

    protected $casts = [
        'production_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Production request belongs to a product
     */
    public function product()
    {
        return $this->belongsTo(Commodity::class, 'product_id');
    }

    /**
     * Production request belongs to a unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Production request has many materials through pivot table
     */
    public function materials()
    {
        return $this->belongsToMany(Commodity::class, 'production_materials', 'production_request_id', 'material_id')
            ->withPivot('required_amount', 'unit_id', 'unit_cost', 'total_cost')
            ->withTimestamps();
    }

    /**
     * Get created date attribute
     */
    public function getCreatedDateAttribute() 
    {
        return $this->created_at->format('Y-m-d');
    }

    /**
     * Get the amount in main unit for each material
     * This is a computed attribute that calculates the equivalent amount in the main unit
     */
    public function getMainUnitAmountAttribute()
    {
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        $mainUnitAmounts = [];
        
        foreach ($this->materials as $material) {
            $selectedUnitId = $material->pivot->unit_id;
            $amountInMainUnit = $commodityUnitService->convertToMainUnit(
                $material,
                $material->pivot->required_amount,
                $selectedUnitId
            );
            
            $mainUnitAmounts[$material->id] = [
                'material_title' => $material->title,
                'original_amount' => $material->pivot->required_amount,
                'original_unit_id' => $selectedUnitId,
                'main_unit_amount' => $amountInMainUnit ?? 0,
                'main_unit_name' => $material->unit ? $material->unit->name : 'نامشخص',
                'main_unit_symbol' => $material->unit ? $material->unit->symbol : '',
            ];
        }
        
        return $mainUnitAmounts;
    }

    // Scopes
    public function scopeAwaitingApproval($query)
    {
        return $query->where('status', 'awaiting_approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approvaled');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['awaiting_approval', 'approvaled', 'approved']);
    }
    
    // Computed Attributes - simplified
    public function getStatusTextAttribute()
    {
        return [
            'awaiting_approval' => 'در انتظار تایید',
            'approvaled' => 'تایید شده',
            'approved' => 'تایید شده', // Legacy support for existing data
            'rejected' => 'رد شده',
            'expired' => 'منقضی شده',
            'done' => 'تکمیل شده',
        ][$this->status] ?? 'نامشخص';
    }

    public function getIsEditableAttribute()
    {
        return in_array($this->status, ['awaiting_approval']);
    }

    public function getIsDeletableAttribute()
    {
        return in_array($this->status, ['awaiting_approval']);
    }

    public function getCanBeApprovedAttribute()
    {
        return $this->status === 'awaiting_approval';
    }

    public function getCanBeRejectedAttribute()
    {
        return $this->status === 'awaiting_approval';
    }

    // Financial calculations - simplified
    public function getTotalInputCostAttribute()
    {
        return $this->materials->sum('pivot.total_cost');
    }
    
    public function getTotalOutputValueAttribute()
    {
        return $this->production_amount * ($this->product->sales_price ?? 0); // This now uses the calculated attribute
    }
    
    public function getProfitAttribute()
    {
        return $this->total_output_value - $this->total_input_cost;
    }

    /**
     * Get profit percentage attribute
     */
    public function getProfitPercentageAttribute()
    {
        if ($this->total_output_value > 0) {
            return ($this->profit / $this->total_output_value) * 100;
        }
        return 0;
    }

    /**
     * Override toArray to exclude computed attributes from activity logging
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Remove computed attributes that shouldn't be logged
        unset($array['status_text']);
        unset($array['is_editable']);
        unset($array['is_deletable']);
        unset($array['can_be_approved']);
        unset($array['can_be_rejected']);
        unset($array['total_input_cost']);
        unset($array['total_output_value']);
        unset($array['profit']);
        unset($array['profit_percentage']);
        unset($array['main_unit_amount']);
        
        return $array;
    }


} 