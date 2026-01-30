<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:attributes,name',
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'نام ویژگی الزامی است.',
            'name.unique' => 'این نام ویژگی قبلا استفاده شده است.',
            'name.max' => 'نام ویژگی نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'description.max' => 'توضیحات نمی‌تواند بیشتر از 1000 کاراکتر باشد.',
        ];
    }
}
