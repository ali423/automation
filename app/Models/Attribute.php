<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, ActivityTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    /**
     * Get the user who created this attribute
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all commodities with this attribute
     */
    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'commodity_attributes')
            ->withTimestamps();
    }
}
