<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $models=array_map(function ($item){
            return  substr($item, 11);
        },array_keys(config('enums.models')));
        $items=implode($models,',');
        return [
            'action'=>['nullable','array'],
            'action.*'=>['in:'.$items],
            'object_id'=>['nullable','integer'],
            'object_type'=>['nullable','required_with:object_id','string','in:'.$items],
        ];
    }
}
