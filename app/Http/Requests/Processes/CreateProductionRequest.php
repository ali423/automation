<?php

namespace App\Http\Requests\Processes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductionRequest extends FormRequest
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
            'product_id' => 'required|exists:commodities,id',
            'amount' => 'required|numeric|min:0.001',
            'comment' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,rar|max:10240',
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
            'product_id.required' => 'محصول باید انتخاب شود.',
            'product_id.exists' => 'محصول انتخاب شده معتبر نیست.',
            'amount.required' => 'مقدار تولید الزامی است.',
            'amount.numeric' => 'مقدار تولید باید عدد باشد.',
            'amount.min' => 'مقدار تولید باید بیشتر از صفر باشد.',
            'comment.max' => 'توضیحات نمی‌تواند بیشتر از ۱۰۰۰ کاراکتر باشد.',
            'file.file' => 'فایل انتخاب شده معتبر نیست.',
            'file.mimes' => 'فایل باید از نوع تصویر، PDF، ZIP یا RAR باشد.',
            'file.max' => 'حجم فایل نمی‌تواند بیشتر از ۱۰ مگابایت باشد.',
        ];
    }
} 