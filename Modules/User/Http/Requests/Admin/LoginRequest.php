<?php

namespace Modules\User\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Modules\User\Enums\ClientEnum;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required',
            'password' => 'required',
            'client_id' => ['required', Rule::in(ClientEnum::ADMIN)],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}
