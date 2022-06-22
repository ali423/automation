<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'source',
        'format',
        'size',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recordChange()
    {
        return $this->morphTo()->withTrashed();
    }
}
