<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'name' => ['required','string'],
            'mobile' => ['required', 'unique:customers,mobile'],
            'comp_name' => ['nullable','string'],
            'address' => ['required',],
            'zip_code' => ['nullable','ir_postal_code'],
            'phone' => ['nullable','ir_phone_with_code','unique:customers,phone'],
            'national_code' => ['nullable','ir_national_code','unique:customers,national_code'],
            'economic_code' => ['nullable','numeric','unique:customers,economic_code'],
        ];
    }
}
