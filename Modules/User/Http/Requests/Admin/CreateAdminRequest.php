<?php

namespace Modules\User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'first_name' => 'required|max:32',
            'last_name' => 'required|max:32',
            'email' => 'required|unique:users|email',
            'role' => 'required|in:2,3,4|exists:roles,id'
        ];

        if (request()->get('add_type', 'password') == 'password') {
            $rules['password'] = 'required|min:3|confirmed';
        }

        return $rules;
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
