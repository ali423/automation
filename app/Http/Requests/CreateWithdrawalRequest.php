<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Commodity;
use App\Services\CommodityUnitService;

class CreateWithdrawalRequest extends FormRequest
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
            'customer_id' => ['required', 'exists:customers,id'],
            'commodity_id' => ['required', 'array', 'min:1'],
            'unit_id' => ['required', 'array', 'min:1'],
            'amount' => ['required', 'array', 'min:1'],
            'price' => ['nullable', 'array'],
            'commodity_id.*' => ['required', 'exists:commodities,id', 'distinct'],
            'unit_id.*' => ['required', 'exists:units,id'],
            'amount.*' => ['required', 'numeric', 'min:0.01'],
            'price.*' => ['nullable', 'numeric'],
            'file' => ['nullable', 'mimes:jpg,svg,png,jpeg,pdf,txt,zip,rar', 'max:5120'],
            'comment' => ['nullable', 'string'],
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
            $unitIds = $this->input('unit_id', []);
            
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
                
                // Ensure only products can be selected for withdrawal requests
                if ($commodity->type !== 'product') {
                    $validator->errors()->add(
                        "commodity_id.{$index}", 
                        'فقط محصولات قابل انتخاب برای درخواست فروش هستند.'
                    );
                    continue;
                }
                
                if (!$commodityUnitService->isUnitSelectable($commodity, $unitId)) {
                    $validator->errors()->add(
                        "unit_id.{$index}", 
                        'The selected unit is not valid for this commodity.'
                    );
                }
                
                // Validate pieces per box value
                // This validation is no longer needed as pieces_per_box is removed
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
            'customer_id.required' => 'انتخاب مشتری الزامی است.',
            'customer_id.exists' => 'مشتری انتخاب شده معتبر نیست.',
            'commodity_id.required' => 'انتخاب حداقل یک محصول الزامی است.',
            'commodity_id.array' => 'فرمت محصولات صحیح نیست.',
            'commodity_id.min' => 'حداقل یک محصول باید انتخاب شود.',
            'commodity_id.*.required' => 'انتخاب محصول الزامی است.',
            'commodity_id.*.exists' => 'محصول انتخاب شده معتبر نیست.',
            'commodity_id.*.distinct' => 'محصول تکراری انتخاب شده است.',
            'unit_id.required' => 'انتخاب واحد الزامی است.',
            'unit_id.array' => 'فرمت واحدها صحیح نیست.',
            'unit_id.min' => 'حداقل یک واحد باید انتخاب شود.',
            'unit_id.*.required' => 'انتخاب واحد الزامی است.',
            'unit_id.*.exists' => 'واحد انتخاب شده معتبر نیست.',
            'amount.required' => 'مقدار کالا الزامی است.',
            'amount.array' => 'فرمت مقادیر صحیح نیست.',
            'amount.min' => 'حداقل یک مقدار باید وارد شود.',
            'amount.*.required' => 'مقدار کالا الزامی است.',
            'amount.*.numeric' => 'مقدار کالا باید عدد باشد.',
            'amount.*.min' => 'مقدار کالا باید بیشتر از صفر باشد.',
            'price.*.numeric' => 'قیمت باید عدد باشد.',
            'file.mimes' => 'فرمت فایل مجاز نیست.',
            'file.max' => 'حجم فایل نباید بیشتر از 5 مگابایت باشد.',
        ];
    }
}
