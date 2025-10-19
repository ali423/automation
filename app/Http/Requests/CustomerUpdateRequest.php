<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerUpdateRequest extends FormRequest
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
            'mobile' => ['required', Rule::unique('customers', 'mobile')->whereNull('deleted_at')->ignore($this->customer->id)],
            'comp_name' => ['nullable','string'],
            'address' => ['required',],
            'zip_code' => ['nullable','ir_postal_code'],
            'phone' => ['nullable','ir_phone_with_code', Rule::unique('customers', 'phone')->whereNull('deleted_at')->ignore($this->customer->id)],
            'national_code' => ['nullable','ir_national_code', Rule::unique('customers', 'national_code')->whereNull('deleted_at')->ignore($this->customer->id)],
            'economic_code' => ['nullable','numeric', Rule::unique('customers', 'economic_code')->whereNull('deleted_at')->ignore($this->customer->id)],
        ];
    }
}
