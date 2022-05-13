<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
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
            'title'=>['required','unique:roles,title,'.$this->role->id,'regex:/(^[A-Za-z0-9 ]+$)+/'],
            'name'=>['required','unique:roles,name,'.$this->role->id,'persian_alpha'],
            'permissions'=>['required','array','min:1'],
            'permissions.*'=> ['exists:permissions,id'],
        ];
    }
}
