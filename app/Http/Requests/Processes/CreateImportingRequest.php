<?php

namespace App\Http\Requests\Processes;

use Illuminate\Foundation\Http\FormRequest;

class CreateImportingRequest extends FormRequest
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
        $rules= [
            'commodity_id'=>['required','array','min:1'],
            'unit'=>['required','array','min:1'],
            'warehouse_id'=>['required','array','min:1'],
            'amount'=>['required','array','min:1'],
            'commodity_id.*'=>['required','exists:commodities,id','distinct'],
            'unit.*'=>['required','in:'.implode(',',array_keys(__('fields.commodity.units')))],
            'warehouse_id.*'=>['required','exists:warehouses,id'],
            'amount.*'=>['required','integer'],
            'file'=>['nullable','mimes:jpg,svg,png,jpeg,pdf,txt,zip,rar','max:5120'],
            'comment'=>['nullable','string'],
        ];
        $commodities=$this->get('commodity_id');
        $units=$this->get('unit');
        $amounts=$this->get('amount');
        $warehouses=$this->get('warehouse_id');
        $array_counts=[
            count($commodities),
            count($units),
            count($amounts),
            count($warehouses),
        ];
        $array_keys=array_merge(array_keys($commodities),array_keys($units),array_keys($amounts),array_keys($warehouses));
        if (count(array_unique($array_counts)) != 1 || count(array_unique($array_keys)) !=   count($commodities) ){
            throw \Illuminate\Validation\ValidationException::withMessages([
                'materials' => ['اطلاعات نوع ماده و مقدار آن باید متناظر باشند.'],
            ]);
        }
        return $rules;
    }
}
