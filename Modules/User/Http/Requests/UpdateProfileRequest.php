<?php

namespace Modules\User\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\User\Enums\GenderEnum;
use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\User\Contracts\Authentication;
use Modules\User\Traits\CanFindUserWithBearerToken;

class UpdateProfileRequest extends BaseFormRequest
{
    use CanFindUserWithBearerToken;

    public function rules()
    {
        $user = $this->findUserWithBearerToken($this->header('Authorization'));

        $rules = [
          'first_name'          => 'required|max:32',
          'last_name'           => 'required|max:32',
          'password'            => 'confirmed|min:6|max:32',
          'group_ids'           => 'required|array|min:1',
          'group_ids.*'         => 'exists:group__groups,id',
          'company_country_id'  => 'required|exists:company__countries,id'
        ];

        if(!$user->hasRoleSlug('trader')) {
          $rules = array_merge($rules ,  [
            'timezone'          => 'required',
            'tag_ids'           => 'required|array|min:1',
            'tag_ids.*'         => 'exists:tag__tags,id',
            'company_name'      => 'required',
            'hour_rate'         => 'required|numeric|between:0,9999.99',
            'day_rate'          => 'required|numeric|between:0,9999.99',
            'languages'         => 'required',
            'availability'      => 'required',
            'education'         => 'required',
            'memberships'       => 'required',
            'past_projects'     => 'required',
            'references'        => 'required',
            'professional_experience' => 'required',
          ]);
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
