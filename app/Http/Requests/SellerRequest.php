<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerRequest extends FormRequest
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
            'mobile' => ['nullable', 'unique:sellers,mobile','ir_mobile:zero'],
            'comp_name' => ['nullable','string'],
            'address' => ['nullable',],
            'zip_code' => ['nullable'],
            'phone' => ['nullable','unique:sellers,phone'],
            'national_code' => ['nullable'],
            'economic_code' => ['nullable'],
        ];
    }
}
