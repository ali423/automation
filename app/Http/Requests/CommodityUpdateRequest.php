<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommodityUpdateRequest extends FormRequest
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
        $rules= [
            'title'=>['required',Rule::unique('commodities', 'title')->ignore($this->commodity->id)],
            'type'=>['required','in:material,product'],
            'warning_limit'=>['required','numeric'],
            'unit_id' =>['required', 'exists:units,id']
        ];

        // For products, allow any unit
        if ($this->get('type') == 'product') {
            $rules['unit_id'] = ['required', 'exists:units,id'];

            $rules['materials']=['required','array','min:1'];
            $rules['materials.*']=['required',Rule::exists('commodities', 'id')->where('type','material'),'distinct'];
            $rules['material_amount']=['required','array','min:1'];
            $rules['material_amount.*']=['required','numeric','min:0.00001'];
            $rules['material_units']=['required','array','min:1'];
            $rules['material_units.*']=['required','exists:units,id']; // Allow any unit for materials
            $rules['sales_price']=['required','integer'];

            // Validate that material amounts are reasonable (not percentage-based validation)
            $materials=$this->get('materials');
            $material_amount=$this->get('material_amount');

            if (count($materials) != count($material_amount) || count(array_intersect_key($materials,$material_amount)) != count($materials) ){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'materials' => ['اطلاعات نوع ماده و مقدار آن باید متناظر باشند.'],
                ]);
            }
        }
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'unit_id.required' => 'واحد باید انتخاب شود.',
            'unit_id.exists' => 'واحد انتخاب شده معتبر نیست.',
        ];
    }
}
