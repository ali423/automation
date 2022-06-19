<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use App\Traits\CommentTrait;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportingRequest extends Model
{
    use HasFactory,SoftDeletes,ActivityTrait,FileTrait,CommentTrait;
    protected $fillable = [
        'commodity_id',
        'amount',
        'file',
        'status',
    ];
}