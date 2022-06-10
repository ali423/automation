<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportationCommodity extends Model
{
    use HasFactory, SoftDeletes, ActivityTrait;

    protected $fillable = [
        'commodity_id',
        'amount',
        'file',
        'status',
    ];

}
