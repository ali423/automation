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
            'amount'=>['required','array','min:1'],
            'amount.*'=>['required','array','min:1'],
            'commodity_id.*'=>['required','exists:commodities,id','distinct'],
            'unit.*'=>['required','in:'.implode(',',array_keys(__('fields.commodity.units')))],
            'amount.*.*'=>['required','integer'],
            'price.*'=>['nullable','numeric'],
            'file'=>['nullable','mimes:jpg,svg,png,jpeg,pdf,txt,zip,rar','max:5120'],
            'comment'=>['nullable','string'],
        ];
    }
}
