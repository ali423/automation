<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'commodity_id'=>['required','exists:commodities,id'],
            'unit'=>['required','in:'.implode(',',array_keys(__('fields.commodity.units')))],
            'deadline'=>['required','shamsi_date'],
            'price'=>['nullable','numeric'],
            'commodity_amount'=>['required','integer'],
        ];
    }
}
