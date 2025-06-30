<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, ActivityTrait;

    protected $fillable = [
        'name',
        'symbol',
    ];

    public function commodities()
    {
        return $this->hasMany(Commodity::class);
    }
}
