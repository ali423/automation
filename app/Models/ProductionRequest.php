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
        'description',
        'status',
        'number',
        'total_cost',
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
    ];


    
    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'production_commodities', 'production_request_id', 'commodity_id')
            ->using(ProductionCommodity::class)
            ->withPivot('type', 'amount', 'unit_id', 'unit_cost', 'total_cost')
            ->with('unit')
            ->withTimestamps();
    }
    
    public function inputMaterials()
    {
        return $this->belongsToMany(Commodity::class, 'production_commodities', 'production_request_id', 'commodity_id')
            ->using(ProductionCommodity::class)
            ->withPivot('type', 'amount', 'unit_id', 'unit_cost', 'total_cost')
            ->with('unit')
            ->withTimestamps()
            ->wherePivot('type', 'input');
    }
    
    public function outputProducts()
    {
        return $this->belongsToMany(Commodity::class, 'production_commodities', 'production_request_id', 'commodity_id')
            ->using(ProductionCommodity::class)
            ->withPivot('type', 'amount', 'unit_id', 'unit_cost', 'total_cost')
            ->with('unit')
            ->withTimestamps()
            ->wherePivot('type', 'output');
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
        return $query->whereIn('status', ['awaiting_approval', 'approvaled']);
    }
    
    // Computed Attributes
    public function getCreatedDateAttribute() 
    {
        return $this->created_at->format('Y-m-d');
    }
    
    public function getTotalInputCostAttribute()
    {
        return $this->inputMaterials->sum('pivot.total_cost');
    }
    
    public function getTotalOutputValueAttribute()
    {
        return $this->outputProducts->sum('pivot.total_cost');
    }
    
    public function getProfitAttribute()
    {
        return $this->total_output_value - $this->total_input_cost;
    }
    
    public function getStatusTextAttribute()
    {
        $statuses = [
            'awaiting_approval' => 'در انتظار تایید',
            'approvaled' => 'تایید شده',
            'rejected' => 'رد شده',
            'expired' => 'منقضی شده',
            'done' => 'تکمیل شده',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    public function getIsEditableAttribute()
    {
        return $this->status === 'awaiting_approval';
    }

    public function getIsDeletableAttribute()
    {
        return $this->status === 'awaiting_approval';
    }

    public function getCanBeApprovedAttribute()
    {
        return $this->status === 'awaiting_approval';
    }

    public function getCanBeRejectedAttribute()
    {
        return $this->status === 'awaiting_approval';
    }

    /**
     * Override toArray method to exclude computed attributes from activity logging
     * This prevents issues with ActivityTrait when dealing with computed attributes
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Remove computed attributes from the array to prevent issues with ActivityTrait
        unset($array['created_date']);
        unset($array['total_input_cost']);
        unset($array['total_output_value']);
        unset($array['profit']);
        unset($array['status_text']);
        unset($array['is_editable']);
        unset($array['is_deletable']);
        unset($array['can_be_approved']);
        unset($array['can_be_rejected']);
        
        // Ensure we have the basic fields needed for activity tracking
        $array['id'] = $this->id;
        $array['status'] = $this->status;
        $array['number'] = $this->number;
        $array['description'] = $this->description;
        $array['total_cost'] = $this->total_cost;
        $array['created_at'] = $this->created_at;
        $array['updated_at'] = $this->updated_at;
        
        // Add relationships data for activity tracking
        if ($this->relationLoaded('commodities')) {
            $array['commodities'] = $this->commodities->toArray();
        }
        
        return $array;
    }

    /**
     * Override getRelatedData method to fix ActivityTrait compatibility
     * This method is called by ActivityTrait during pivot events
     */
    public static function getRelatedData($item, $relationName, $pivotIdsAttributes)
    {
        $changed_relations = array_column($item->$relationName()->whereIn('id', $pivotIdsAttributes)->get()->toArray(), 'pivot');
        $relation_data = config('enums.models')[get_class($item)]['relations'][$relationName] ?? null;
        $pivot_exits = $relation_data['pivots'] ?? null;
        
        if (!empty($pivot_exits)) {
            foreach ($changed_relations as $value) {
                foreach (array_keys($pivot_exits) as $pivot) {
                    $pivot_res[$value[$relation_data['primary_key']]]['pivots'][$pivot] = $value[$pivot];
                }
            }
        } else {
            $pivot_res = $pivotIdsAttributes;
        }
        
        return $pivot_res ?? null;
    }

    // ActivityTrait compatibility methods - these are called by the trait
    public static function createActivity()
    {
        // This method is called by ActivityTrait but we don't need to implement it
        // since the trait handles it internally
    }

    public static function updateActivity()
    {
        // This method is called by ActivityTrait but we don't need to implement it
        // since the trait handles it internally
    }

    public static function deleteActivity()
    {
        // This method is called by ActivityTrait but we don't need to implement it
        // since the trait handles it internally
    }

    public static function pivotActivity()
    {
        // This method is called by ActivityTrait but we don't need to implement it
        // since the trait handles it internally
    }

    public static function pivotSyncActivity()
    {
        // This method is called by ActivityTrait but we don't need to implement it
        // since the trait handles it internally
    }

    public static function pivotAttachActivity()
    {
        // This method is called by ActivityTrait but we don't need to implement it
        // since the trait handles it internally
    }

    public static function pivotDetachActivity()
    {
        // This method is called by ActivityTrait but we don't need to implement it
        // since the trait handles it internally
    }

    public static function pivotUpdateActivity()
    {
        // This method is called by ActivityTrait but we don't need to implement it
        // since the trait handles it internally
    }
} 