<?php

namespace App\Http\Requests\Processes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        return [
            'commodity_id'=>['required','array','min:1'],
            'seller_id'=>['required','exists:sellers,id'],
            'unit'=>['required','array','min:1'],
            'warehouse_id'=>['required','array','min:1'],
            'amount'=>['required','array','min:1'],
            'commodity_id.*'=>['required','exists:commodities,id','distinct'],
            'unit.*'=>['required','in:'.implode(',',array_keys(__('fields.commodity.units')))],
            'purchase_price.*'=>['nullable','numeric'],
            'warehouse_id.*'=>['required','exists:warehouses,id'],
            'amount.*'=>['required','integer'],
            'file'=>['nullable','mimes:jpg,svg,png,jpeg,pdf,txt,zip,rar','max:5120'],
            'comment'=>['nullable','string'],
        ];
    }
}
