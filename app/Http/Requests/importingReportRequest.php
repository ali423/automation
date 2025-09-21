<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class importingReportRequest extends FormRequest
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
            'commodity_id'=>['required',Rule::exists('commodities', 'id')->where('type','material')],
            'date_from'=>['required','shamsi_date'],
            'date_to'=>['required','shamsi_date','after_or_equal:date_from'],
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
            'commodity_id.required' => 'انتخاب کالا الزامی است.',
            'commodity_id.exists' => 'کالای انتخاب شده معتبر نیست.',
            'date_from.required' => 'تاریخ شروع الزامی است.',
            'date_from.shamsi_date' => 'فرمت تاریخ شروع صحیح نیست.',
            'date_to.required' => 'تاریخ پایان الزامی است.',
            'date_to.shamsi_date' => 'فرمت تاریخ پایان صحیح نیست.',
            'date_to.after_or_equal' => 'تاریخ پایان باید بعد از یا برابر تاریخ شروع باشد.',
        ];
    }
}
