<?php

namespace Modules\User\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\User\Enums\ClientEnum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Internationalisation\BaseFormRequest;

class ExpertRegisterRequest extends BaseFormRequest
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
            'timezone' => 'required',
            'company_country_id' => 'required|exists:company__countries,id',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:group__groups,id',
            'tag_ids' => 'required|array|min:1',
            'tag_ids.*' => 'exists:tag__tags,id',
            'company_name' => 'required_if:has_company,true',
            'company_vat_no' => 'required_if:company_vat_registered,true',
            'hour_rate' => 'required|numeric|between:0,9999.99',
            'day_rate' => 'required|numeric|between:0,9999.99',
            'languages' => 'required',
            'availability' => 'required',
            'education' => 'required',
            'memberships' => 'required',
            'professional_experience' => 'required',
            'past_projects' => 'required',
            'references' => 'required',
            'marketing' => 'required'
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
        return [
          'company_vat_no.required_if' => 'The company vat no field is required when company vat registered.'
        ];
    }
}
