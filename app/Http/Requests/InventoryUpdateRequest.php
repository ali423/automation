<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Commodity;
use App\Services\CommodityUnitService;

class InventoryUpdateRequest extends FormRequest
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
            'commodity_id' => 'required|exists:commodities,id',
            'unit_id' => 'required|exists:units,id',
            'amount' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0.01',
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
            $commodityId = $this->input('commodity_id');
            $unitId = $this->input('unit_id');
            
            if (!$commodityId || !$unitId) {
                return;
            }
            
            $commodity = Commodity::find($commodityId);
            if (!$commodity) {
                return;
            }
            
            // Check if the unit is the main unit of the commodity
            if ($commodity->unit_id == $unitId) {
                return; // Main unit is always valid
            }
            
            // Check if there's a valid unit conversion
            $commodityUnitService = app(CommodityUnitService::class);
            if (!$commodityUnitService->isUnitSelectable($commodity, $unitId)) {
                $validator->errors()->add(
                    'unit_id', 
                    'واحد انتخاب شده برای این کالا معتبر نیست. فقط واحد اصلی یا واحدهای دارای تبدیل معتبر هستند.'
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'commodity_id.required' => 'کالا الزامی است.',
            'commodity_id.exists' => 'کالای انتخاب شده معتبر نیست.',
            'unit_id.required' => 'واحد الزامی است.',
            'unit_id.exists' => 'واحد انتخاب شده معتبر نیست.',
            'amount.required' => 'مقدار موجودی الزامی است.',
            'amount.numeric' => 'مقدار موجودی باید عددی باشد.',
            'amount.min' => 'مقدار موجودی نمی‌تواند منفی باشد.',
            'purchase_price.required' => 'قیمت خرید الزامی است.',
            'purchase_price.numeric' => 'قیمت خرید باید عددی باشد.',
            'purchase_price.min' => 'قیمت خرید باید بیشتر از صفر باشد.',
        ];
    }
} 