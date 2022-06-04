<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commodity extends Model
{
    use HasFactory,ActivityTrait,SoftDeletes;

    protected $fillable = [
        'number',
        'title',
        'amount',
        'type',
    ];
}
