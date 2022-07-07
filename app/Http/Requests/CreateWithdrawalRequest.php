<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateWithdrawalRequest extends FormRequest
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
            'customer_id'=>['required','exists:customers,id'],
            'commodity_id'=>['required','array','min:1'],
            'unit'=>['required','array','min:1'],
            'price'=>['nullable','array'],
            'warehouse_id'=>['required','array','min:1'],
            'warehouse_id.*'=>['required','array','min:1'],
            'amount'=>['required','array','min:1'],
            'amount.*'=>['required','array','min:1'],
            'commodity_id.*'=>['required','exists:commodities,id','distinct'],
            'unit.*'=>['required','in:'.implode(',',array_keys(__('fields.commodity.units')))],
            'warehouse_id.*.*'=>['required','exists:warehouses,id'],
            'amount.*.*'=>['required','integer'],
            'price.*'=>['nullable','numeric'],
            'file'=>['nullable','mimes:jpg,svg,png,jpeg,pdf,txt,zip,rar','max:5120'],
            'comment'=>['nullable','string'],
        ];
        $commodities=$this->get('commodity_id');
        $units=$this->get('unit');
        $array_counts=[
            count($commodities),
            count($units),
        ];
        $array_keys=array_merge(array_keys($commodities),array_keys($units));
        if (count(array_unique($array_counts)) != 1 || count(array_unique($array_keys)) !=   count($commodities) ){
            throw \Illuminate\Validation\ValidationException::withMessages([
                'materials' => ['اطلاعات نوع ماده و مقدار آن باید متناظر باشند.'],
            ]);
        }
        return $rules;
    }
}
