<?php

namespace Modules\User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => "required|email|unique:users,email,{$this->id}",
            'first_name' => 'required|max:32',
            'last_name' => 'required|max:32',
            'role' => 'required|in:2,3,4|exists:roles,id',
            'password' => 'confirmed'
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'avatar.max' => 'The avatar must not exceed 3MB.'
        ];
    }
}
