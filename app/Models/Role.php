<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory,SoftDeletes,ActivityTrait;


    protected $fillable = ['title','name'];

    public function permissions(){
        return $this->belongsToMany(Permission::class,'role_permissions')->withTimestamps();
    }
    public function havePermission($permission){
        return $this->permissions()->where('title',$permission)->exists();
    }

    public function users(){
        return $this->hasMany(User::class);
    }

    public function getRelationsDataAttribute()
    {
        return[
          'permissions'=>$this->permissions()->select(['id','name','title'])->get()->toArray(),
        ];
    }

}
