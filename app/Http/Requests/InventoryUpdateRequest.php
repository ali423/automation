<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryUpdateRequest extends FormRequest
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
            'commodity_id' => 'required|exists:commodities,id',
            'unit_id' => 'required|exists:units,id',
            'amount' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'commodity_id.required' => 'کالا الزامی است.',
            'commodity_id.exists' => 'کالای انتخاب شده معتبر نیست.',
            'unit_id.required' => 'واحد الزامی است.',
            'unit_id.exists' => 'واحد انتخاب شده معتبر نیست.',
            'amount.required' => 'مقدار موجودی الزامی است.',
            'amount.numeric' => 'مقدار موجودی باید عددی باشد.',
            'amount.min' => 'مقدار موجودی نمی‌تواند منفی باشد.',
            'purchase_price.required' => 'قیمت خرید الزامی است.',
            'purchase_price.numeric' => 'قیمت خرید باید عددی باشد.',
            'purchase_price.min' => 'قیمت خرید باید بیشتر از صفر باشد.',
        ];
    }
} 