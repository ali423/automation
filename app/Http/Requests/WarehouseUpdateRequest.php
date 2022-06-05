<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseUpdateRequest extends FormRequest
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
        return [
            'title'=>['required','unique:warehouses,title,'.$this->warehouse->id],
            'type'=>['required','in:tank,hall'],
            'status'=>['required','in:active,inactive'],
            'capacity'=>['required','integer'],
        ];
    }
}
