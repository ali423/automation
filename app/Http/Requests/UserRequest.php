<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'user_name'=>['required','string','regex:/(^[A-Za-z0-9 ]+$)+/','unique:users,user_name'],
            'status'=>['required','in:active,inactive'],
            'role'=>['required','exists:roles,id'],
            'password'=>['required','confirmed','min:5','max:50'],
            'warning_message'=>['nullable','in:on'],
            'mobile'=>['nullable','required_with:warning_message,on','unique:users,mobile','ir_mobile:zero'],
        ];
    }
}
