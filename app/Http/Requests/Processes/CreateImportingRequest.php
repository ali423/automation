<?php

namespace App\Http\Requests\Processes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Commodity;
use App\Services\CommodityUnitService;

class CreateImportingRequest extends FormRequest
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
            'commodity_id'=>['required','array','min:1'],
            'seller_id'=>['required','exists:sellers,id'],
            'unit'=>['required','array','min:1'],
            'amount'=>['required','array','min:1'],
            'commodity_id.*'=>['required','exists:commodities,id','distinct', Rule::exists('commodities', 'id')->where('type', 'material')],
            'unit.*'=>['required','exists:units,id'],
            'purchase_price.*'=>['nullable','numeric'],
            'amount.*'=>['required','integer'],
            'file'=>['nullable','mimes:jpg,svg,png,jpeg,pdf,txt,zip,rar','max:5120'],
            'comment'=>['nullable','string'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $commodityIds = $this->input('commodity_id', []);
            $unitIds = $this->input('unit', []);
            
            if (count($commodityIds) !== count($unitIds)) {
                return;
            }
            
            $commodityUnitService = app(CommodityUnitService::class);
            
            foreach ($commodityIds as $index => $commodityId) {
                if (!isset($unitIds[$index])) {
                    continue;
                }
                
                $commodity = Commodity::find($commodityId);
                $unitId = $unitIds[$index];
                
                if (!$commodity) {
                    continue;
                }
                
                if (!$commodityUnitService->isUnitSelectable($commodity, $unitId)) {
                    $validator->errors()->add(
                        "unit.{$index}", 
                        'The selected unit is not valid for this commodity.'
                    );
                }
            }
        });
    }
}
