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
            'title'=>['required','unique:commodities,title,'.$this->commodity->id],
            'warning_limit'=>['required','numeric'],

        ];
        if ($this->commodity->type == 'product'){
            $rules['materials']=['required','array','min:1'];
            $rules['materials.*']=['required',Rule::exists('commodities', 'id')->where('type','material'),'distinct'];
            $rules['material_amount']=['required','array','min:1'];
            $rules['material_amount.*']=['required','numeric','max:100'];
            $rules['sales_price']=['required','integer'];

            $total_materials=array_sum($this->get('material_amount'));
            if ($total_materials > 100 ){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'material_amount' => ['مجموع مقدار مواد تشکیل دهنده نباید از صد بیشتر باشد.'],
                ]);
            }
            $materials=$this->get('materials');
            $material_amount=$this->get('material_amount');

            if (count($materials) != count($material_amount) || count(array_intersect_key($materials,$material_amount)) != count($materials) ){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'materials' => ['اطلاعات نوع ماده و مقدار آن باید متناظر باشند.'],
                ]);
            }
        }else{
            $rules['purchase_price']=['required','integer'];
        }
        return  $rules;
    }
}
