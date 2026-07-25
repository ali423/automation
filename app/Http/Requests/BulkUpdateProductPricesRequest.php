<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateProductPricesRequest extends FormRequest
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

        foreach ($prices as $key => $item) {
            if (!is_array($item)) {
                continue;
            }

            if (array_key_exists('discount_percentage', $item) && $item['discount_percentage'] === '') {
                $prices[$key]['discount_percentage'] = null;
            }
        }

        $this->merge(['prices' => array_values($prices)]);
    }

    public function rules()
    {
        return [
            'prices' => ['required', 'array', 'min:1'],
            'prices.*.id' => ['required', 'integer', 'exists:commodities,id'],
            'prices.*.sales_price' => ['required', 'integer', 'min:0'],
            'prices.*.discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages()
    {
        return [
            'prices.required' => 'لیست قیمت‌ها الزامی است.',
            'prices.*.sales_price.required' => 'قیمت فروش الزامی است.',
            'prices.*.sales_price.integer' => 'قیمت فروش باید عدد صحیح باشد.',
            'prices.*.sales_price.min' => 'قیمت فروش نمی‌تواند منفی باشد.',
            'prices.*.discount_percentage.integer' => 'درصد تخفیف باید عدد صحیح باشد.',
            'prices.*.discount_percentage.max' => 'درصد تخفیف نمی‌تواند بیشتر از ۱۰۰ باشد.',
        ];
    }
}
