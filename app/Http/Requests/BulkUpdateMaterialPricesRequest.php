<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateMaterialPricesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $prices = $this->input('prices', []);

        if (!is_array($prices)) {
            return;
        }

        $this->merge(['prices' => array_values($prices)]);
    }

    public function rules()
    {
        return [
            'prices' => ['required', 'array', 'min:1'],
            'prices.*.id' => ['required', 'integer', 'exists:commodities,id'],
            'prices.*.purchase_price' => ['required', 'numeric', 'min:100'],
        ];
    }

    public function messages()
    {
        return [
            'prices.required' => 'لیست قیمت‌ها الزامی است.',
            'prices.*.purchase_price.required' => 'قیمت خرید الزامی است.',
            'prices.*.purchase_price.numeric' => 'قیمت خرید باید عددی باشد.',
            'prices.*.purchase_price.min' => 'قیمت خرید باید حداقل ۱۰۰ باشد.',
        ];
    }
}
