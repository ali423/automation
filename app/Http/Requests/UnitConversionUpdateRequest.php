<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitConversionUpdateRequest extends FormRequest
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
            'from_unit_id' => ['required', 'exists:units,id'],
            'to_unit_id' => ['required', 'exists:units,id', 'different:from_unit_id'],
            'conversion_rate' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'from_unit_id.required' => 'انتخاب واحد مبدا الزامی است.',
            'from_unit_id.exists' => 'واحد مبدای انتخاب شده معتبر نیست.',
            'to_unit_id.required' => 'انتخاب واحد مقصد الزامی است.',
            'to_unit_id.exists' => 'واحد مقصد انتخاب شده معتبر نیست.',
            'to_unit_id.different' => 'واحد مبدا و مقصد نمی‌توانند یکسان باشند.',
            'conversion_rate.required' => 'وارد کردن نرخ تبدیل الزامی است.',
            'conversion_rate.numeric' => 'نرخ تبدیل باید عدد باشد.',
            'conversion_rate.min' => 'نرخ تبدیل باید بزرگتر از صفر باشد.',
        ];
    }

    /**
     * Get validated data.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->all();
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
            $conversion = $this->route('unit_conversion');
            if ($conversion instanceof \Illuminate\Database\Eloquent\Collection) {
                $conversion = $conversion->first();
            }
            if (is_numeric($conversion)) {
                $conversion = \App\Models\UnitConversion::find($conversion);
            }
            if (!$conversion || !is_object($conversion)) {
                $validator->errors()->add('conversion', 'تبدیل واحد یافت نشد.');
                return;
            }
            $existingConversion = \App\Models\UnitConversion::where([
                'commodity_id' => $conversion->commodity_id,
                'from_unit_id' => $this->from_unit_id,
                'to_unit_id' => $this->to_unit_id,
            ])->where('id', '!=', $conversion->id)->first();

            if ($existingConversion) {
                $validator->errors()->add('conversion', 'این تبدیل واحد قبلاً برای این کالا تعریف شده است.');
            }
        });
    }
} 