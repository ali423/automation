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
        'pivot_data',
        'relation_model',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function recordChange()
    {
        return $this->morphTo();
    }

    public function perviousActivity()
    {
        return $this->belongsTo(Activity::class, 'previous_activity_id');
    }

    public function getActionPersianNameAttribute()
    {
        return config('enums.activity_types')[$this->action];
    }

    public function getRelationPersianNameAttribute()
    {
        return config('enums.models')[$this->record_change_type]['relations'][$this->relation_name] ?? null;
    }
    public function getChangesAttribute(){
        switch ($this->action) {
            case "create":
                return false;
            case "update":
                $old_value=json_decode($this->perviousActivity->data,true);
                $new_value=json_decode($this->data,true);
                unset($old_value['permissions'],$old_value['updated_at']);
                unset($new_value['permissions'],$new_value['updated_at']);
                $old_diff=array_diff($old_value,$new_value);
                $new_diff=array_diff($new_value,$old_value);
               return [
                   'old_value'=>$old_diff,
                   'new_value'=>$new_diff,
               ];
            case "attach":
                $value=json_decode($this->pivot_data,true);
                $data['attached']=array_map(function ($item){
                     return $this->relation_model::where('id',$item)->first()->toArray();
                },$value);
                return $data;
            case "sync":
                $value=json_decode($this->pivot_data,true);
                $data['attached']=array_map(function ($item){
                    return $this->relation_model::where('id',$item)->first()->toArray();
                },$value['attached']);
                $data['detached']=array_map(function ($item){
                    return $this->relation_model::where('id',$item)->first()->toArray();
                },$value['detached']);
                return $data;
            case "detach":
                $value=json_decode($this->pivot_data,true);
                $data['detached']=array_map(function ($item){
                    return $this->relation_model::where('id',$item)->first()->toArray();
                },$value);
                return $data;
            default:
               return null;
        }

    }
}
