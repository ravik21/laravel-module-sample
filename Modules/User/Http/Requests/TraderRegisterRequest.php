<?php

namespace Modules\User\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\User\Enums\ClientEnum;
use Illuminate\Foundation\Http\FormRequest;

class TraderRegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'max:32|required',
            'last_name' => 'max:32|required',
            'email' => 'email|required|max:255|unique:users,email',
            'password' => 'required|confirmed|min:6|max:32',
            'password_confirmation' => 'required',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:group__groups,id',
            'company_country_id' => 'required|exists:company__countries,id'
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
