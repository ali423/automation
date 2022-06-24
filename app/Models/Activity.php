<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\isNull;

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
        'relation_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recordChange()
    {
        return $this->morphTo()->withTrashed();
    }

    public function perviousActivity()
    {
        return $this->belongsTo(Activity::class, 'previous_activity_id');
    }

    public function pivotName($pivot){
       return config('enums.models')[$this->record_change_type]['relations'][$this->relation_name]['pivots'][$pivot];
    }

    public function getActionPersianNameAttribute()
    {
        return config('enums.activity_types')[$this->action];
    }

    public function getRelationPersianNameAttribute()
    {
        return config('enums.models')[$this->record_change_type]['relations'][$this->relation_name]['fa_name'] ?? null;
    }

    public function getChangesAttribute()
    {
        switch ($this->action) {
            case "create":
                return false;
            case "update":
                $old_value = json_decode($this->perviousActivity->data, true);
                $new_value = json_decode($this->data, true);
                unset($old_value[$this->relation_name], $old_value['updated_at']);
                unset($new_value[$this->relation_name], $new_value['updated_at']);
                $old_diff = array_diff($old_value, $new_value);
                $new_diff = array_diff($new_value, $old_value);
                $res = array_filter(array_keys($new_diff), function ($item) use ($new_diff, $old_diff) {
                    if (!empty($new_diff[$item])) {
                        return $item;
                    } else {
                        if (!empty($old_diff[$item])) {
                            return $item;
                        }
                    }
                });
                foreach ($res as $value) {
                    $res_old_diff[$value] = $old_diff[$value];
                    $res_new_diff[$value] = $new_diff[$value];
                }
                return [
                    'old_value' => $res_old_diff ?? null,
                    'new_value' => $res_new_diff ?? null,
                ];
            case "attach":
                $value = json_decode($this->pivot_data, true);
                $data['attached']=$this->calculatePivotValues($value);
                return $data ?? null;
            case "sync":
                $value = json_decode($this->pivot_data, true);
                $data['attached']=$this->calculatePivotValues($value['attached']);
                $data['detached']=$this->calculatePivotValues($value['detached']);
                $data['updated']=$this->calculatePivotValues($value['updated']);
                return $data;
            case "detach":
                $value = json_decode($this->pivot_data, true);
                $data['detached']=$this->calculatePivotValues($value);
                return $data;
            default:
                return null;
        }

    }

    protected function calculatePivotValues($items){
        foreach ($items ?? array() as $key=>$value){
            if (is_array($value)){
                $search_obj=$key;
            }else{
                $search_obj=$value;
            }
//            dd($this->record_change_type);
            $result= $this->record_change_type::where('id', $search_obj)->first()->toArray();
            if (!empty($value['pivots'])){
                $result['pivots']=$value['pivots'];
            }
            $data[] = $result;
        }
        return $data ?? null;
    }
}
