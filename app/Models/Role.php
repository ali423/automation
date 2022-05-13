<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;


    protected $fillable = ['title','name'];

    public function permissions(){
        return $this->belongsToMany(Permission::class,'role_permissions');
    }
    public function havePermission($permission){
        return $this->Permissions()->where('id',$permission->id)->exists();
    }

    public function users(){
        return $this->hasMany(User::class);
    }
}
