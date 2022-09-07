<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seller extends Model
{
    use HasFactory, ActivityTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'mobile',
        'comp_name',
        'address',
        'zip_code',
        'phone',
        'national_code',
        'economic_code',
    ];
}
