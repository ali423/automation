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

    public function scopeManualStockAdjustments($query)
    {
        return $query->where('data->adjustment_type', 'stock_adjustment');
    }

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
        // Check if this is a custom adjustment activity
        $data = json_decode($this->data, true);
        if (isset($data['adjustment_type'])) {
            return [
                'custom_adjustment' => true,
                'adjustment_type' => $data['adjustment_type'],
                'operation' => $data['operation'] ?? null,
                'quantity' => $data['quantity'] ?? null,
                'old_amount' => $data['old_amount'] ?? null,
                'new_amount' => $data['new_amount'] ?? null,
                'old_price' => $data['old_price'] ?? null,
                'new_price' => $data['new_price'] ?? null,
                'reason' => $data['reason'] ?? null,
            ];
        }

        switch ($this->action) {
            case "create":
                return false;
            case "update":
                if (!$this->perviousActivity) {
                    return [
                        'old_value' => null,
                        'new_value' => null,
                    ];
                }
                $old_value = json_decode($this->perviousActivity->data, true);
                $new_value = json_decode($this->data, true);
                unset($old_value[$this->relation_name], $old_value['updated_at']);
                unset($new_value[$this->relation_name], $new_value['updated_at']);
                unset($old_value['pivot']);
                unset($new_value['pivot']);
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
                    $res_old_diff[$value]= $old_diff[$value]?? '(بدون مقدار)';
                    $res_new_diff[$value] = $new_diff[$value]?? '(بدون مقدار)';
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
            case "pivot_update":
               $model_data= config('enums.models.'.$this->record_change_type);
               $i=0;
              foreach (json_decode($this->pivot_data,true) as $key=>$value){
                  $pivot_update_res[$i]['title']=$this->relation_model::where('id', $key)->first()->title;
                  $pivot_update_res[$i]['relation_name']=$model_data['relations'][$this->relation_name]['fa_name'];
                  foreach ($value['pivots'] as $key=>$value){
                      $pivot_update_res[$i]['changes'][]= [
                          'pivot_title'=>$model_data['relations'][$this->relation_name]['pivots'][$key],
                          'new_amount'=>$value,
                      ];
                  }
                  $i++;
              }
                return [
                    'pivot_update'=>$pivot_update_res,
                ];
            default:
                return null;
        }

    }

    protected function calculatePivotValues($items){
        $data = [];
        
        foreach ($items ?? array() as $key=>$value){
            if (is_array($value)){
                $search_obj=$key;
            }else{
                $search_obj=$value;
            }
            $result= $this->record_change_type::where('id', $search_obj)->first();
            if (!is_null($result)){
                $result=$result->toArray();
                if (!empty($value['pivots'])){
                    $result['pivots']=$value['pivots'];
                }
                $data[] = $result;
            }
        }
        return $data ?? null;
    }
}
