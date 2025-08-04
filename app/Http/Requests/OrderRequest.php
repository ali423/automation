<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Commodity;
use App\Services\CommodityUnitService;

class OrderRequest extends FormRequest
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
            'deadline' => ['required', 'shamsi_date'],
            'price' => ['nullable', 'array'],
            'commodity_amount' => ['required', 'array', 'min:1'],
            'commodity_id.*' => ['required', 'exists:commodities,id', 'distinct', Rule::exists('commodities', 'id')->where('type', 'product')],
            'unit_id.*' => ['required', 'exists:units,id'],
            'price.*' => ['nullable', 'numeric'],
            'commodity_amount.*' => ['required', 'integer', 'min:1'],
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
                
                // Check if the unit is valid for this commodity
                $selectableUnits = $commodityUnitService->getSelectableUnits($commodity);
                $isValidUnit = $selectableUnits->contains('id', $unitId);
                
                if (!$isValidUnit) {
                    $validator->errors()->add(
                        "unit_id.{$index}", 
                        'واحد انتخاب شده برای این کالا معتبر نیست.'
                    );
                }
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
            'commodity_id.required' => 'انتخاب حداقل یک کالا الزامی است.',
            'commodity_id.array' => 'فرمت کالاها صحیح نیست.',
            'commodity_id.min' => 'حداقل یک کالا باید انتخاب شود.',
            'commodity_id.*.required' => 'انتخاب کالا الزامی است.',
            'commodity_id.*.exists' => 'کالای انتخاب شده معتبر نیست.',
            'commodity_id.*.distinct' => 'کالای تکراری انتخاب شده است.',
            'unit_id.required' => 'انتخاب واحد الزامی است.',
            'unit_id.array' => 'فرمت واحدها صحیح نیست.',
            'unit_id.min' => 'حداقل یک واحد باید انتخاب شود.',
            'unit_id.*.required' => 'انتخاب واحد الزامی است.',
            'unit_id.*.exists' => 'واحد انتخاب شده معتبر نیست.',
            'deadline.required' => 'تاریخ مهلت الزامی است.',
            'deadline.shamsi_date' => 'فرمت تاریخ مهلت صحیح نیست.',
            'price.*.numeric' => 'قیمت باید عدد باشد.',
            'commodity_amount.required' => 'مقدار کالا الزامی است.',
            'commodity_amount.array' => 'فرمت مقادیر صحیح نیست.',
            'commodity_amount.min' => 'حداقل یک مقدار باید وارد شود.',
            'commodity_amount.*.required' => 'مقدار کالا الزامی است.',
            'commodity_amount.*.integer' => 'مقدار کالا باید عدد صحیح باشد.',
            'commodity_amount.*.min' => 'مقدار کالا باید بیشتر از صفر باشد.',
            'file.mimes' => 'فرمت فایل مجاز نیست.',
            'file.max' => 'حجم فایل نباید بیشتر از 5 مگابایت باشد.',
        ];
    }
}
