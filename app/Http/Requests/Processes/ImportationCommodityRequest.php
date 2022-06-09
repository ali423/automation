<?php

namespace App\Http\Requests\Processes;

use Illuminate\Foundation\Http\FormRequest;

class ImportationCommodityRequest extends FormRequest
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
            'commodity_id'=>['required','exists:commodities,id'],
            'warehouse_id'=>['required','exists:warehouses,id'],
            'unit'=>['required','in:'.implode(',',array_keys( __('fields.commodity.units')))],
            'capacity'=>['required','integer'],
            'file' => ['nullable', 'mimes:jpg,jpeg,png,bmp,svg,pdf,zip,rar', 'max:8192'],
        ];
    }
}
