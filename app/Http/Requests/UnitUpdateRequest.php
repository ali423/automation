<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:units,name,' . $this->unit->id],
            'symbol' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'وارد کردن نام واحد الزامی است.',
            'name.unique' => 'این نام واحد قبلاً ثبت شده است.',
            'name.max' => 'نام واحد نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'symbol.required' => 'وارد کردن نماد واحد الزامی است.',
            'symbol.max' => 'نماد واحد نباید بیشتر از ۱۰ کاراکتر باشد.',
        ];
    }
} 