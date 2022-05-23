<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'record_change_id',
        'record_change_type',
        'action',
        'data',
        'relations_data',
        'previous_activity_id',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function recordChange()
    {
        return $this->morphTo();
    }
}
