<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerUpdateRequest extends FormRequest
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
            'mobile' => ['nullable', 'unique:sellers,mobile,'.$this->seller->id],
            'comp_name' => ['nullable','string'],
            'address' => ['nullable',],
            'zip_code' => ['nullable','ir_postal_code'],
            'phone' => ['nullable','ir_phone_with_code','unique:sellers,phone,'.$this->seller->id],
            'national_code' => ['nullable','ir_national_code','unique:sellers,national_code,'.$this->seller->id],
            'economic_code' => ['nullable','numeric','unique:sellers,economic_code,'.$this->seller->id],

        ];
    }
}
