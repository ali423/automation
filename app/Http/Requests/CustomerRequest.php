<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'mobile' => ['required', 'ir_mobile:zero', Rule::unique('customers', 'mobile')->whereNull('deleted_at')],
            'comp_name' => ['nullable','string'],
            'address' => ['required',],
            'zip_code' => ['nullable','numeric','digits:10'],
            'phone' => ['nullable','ir_phone_with_code', Rule::unique('customers', 'phone')->whereNull('deleted_at')],
            'national_code' => ['nullable','numeric','digits:10', Rule::unique('customers', 'national_code')->whereNull('deleted_at')],
            'economic_code' => ['nullable','numeric','digits:12', Rule::unique('customers', 'economic_code')->whereNull('deleted_at')],
        ];
    }
}
