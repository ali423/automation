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
            'mobile' => ['required', 'unique:customers,mobile','ir_mobile:zero'],
            'comp_name' => ['nullable','string'],
            'address' => ['required',],
            'zip_code' => ['nullable','numeric','digits:10'],
            'phone' => ['nullable','ir_phone_with_code','unique:customers,phone'],
            'national_code' => ['nullable','numeric','digits:10','unique:customers,national_code'],
            'economic_code' => ['nullable','numeric','digits:12','unique:customers,economic_code'],
        ];
    }
}
