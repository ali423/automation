<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>['required','string'],
            'lastname'=>['required','string'],
            'status'=>['required','in:active,inactive'],
            'role'=>['required','exists:roles,id'],
            'warning_message'=>['nullable','in:on'],
            'mobile'=>['nullable','required_with:warning_message,on','unique:users,mobile,'.$this->user->id,'ir_mobile:zero'],
        ];
    }
}
