<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Commodity; // Added this import for the new validation logic

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
            'unit_id' =>['required', 'exists:units,id'],
        ];

        // For products, pieces_per_box is required
        if ($this->get('type') == 'product') {
            $rules['pieces_per_box'] = ['required','integer','min:1'];
            $rules['unit_id'] = ['required', 'exists:units,id'];
            $rules['product_identifier'] = ['required','string','max:255',Rule::unique('commodities', 'product_identifier')->ignore($this->commodity->id)];
            $rules['weight_per_unit'] = ['required','numeric','min:0.001'];
            $rules['litrage'] = ['nullable','numeric','min:0'];

            $rules['materials']=['required','array','min:1'];
            $rules['materials.*']=['required',Rule::exists('commodities', 'id')->where('type','material'),'distinct'];
            $rules['material_amount']=['required','array','min:1'];
            $rules['material_amount.*']=['required','numeric','min:0.00001'];
            $rules['material_units']=['required','array','min:1'];
            $rules['material_units.*']=['required','exists:units,id']; // Allow any unit for materials
            $rules['sales_price']=['required','numeric','min:0'];

            // Validate that material amounts are reasonable (not percentage-based validation)
            $materials=$this->get('materials');
            $material_amount=$this->get('material_amount');
            $material_units=$this->get('material_units');
            
            foreach ($materials as $key => $materialId) {
                if (isset($material_amount[$key]) && isset($material_units[$key])) {
                    $amount = $material_amount[$key];
                    $unitId = $material_units[$key];
                    
                    // Get the material to check its unit
                    $material = Commodity::find($materialId);
                    if ($material && $material->unit_id == $unitId) {
                        // Same unit validation - amount should be reasonable
                        if ($amount > 1000) {
                            $rules['material_amount.'.$key][] = 'max:1000';
                        }
                    }
                }
            }
        } else {
            // For materials, pieces_per_box is not required
            $rules['purchase_price'] = ['required','numeric','min:100'];
            $rules['weight_per_unit'] = ['nullable','numeric','min:0.001'];
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
