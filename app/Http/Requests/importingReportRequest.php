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
            'date_from'=>['required','shamsi_date','date'],
            'date_to'=>['required','shamsi_date','date','after_or_equal:date_from'],
        ];
    }
}
