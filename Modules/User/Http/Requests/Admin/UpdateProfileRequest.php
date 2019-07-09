<?php

namespace Modules\User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Contracts\Authentication;

class UpdateProfileRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => "required|email|unique:users,email,{$this->id}",
            'first_name' => 'required|max:32',
            'last_name' => 'required|max:32',
            'password' => 'confirmed'
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}
