<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'commodity_id',
        'unit_id',
        'quantity',
        'purchase_price',
        'sale_price',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'amount' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2'
    ];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }


    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeRawMaterials($query)
    {
        return $query->whereHas('commodity', function($q) {
            $q->where('type', 'raw_material');
        });
    }

    public function scopeProducts($query)
    {
        return $query->whereHas('commodity', function($q) {
            $q->where('type', 'product');
        });
    }
}