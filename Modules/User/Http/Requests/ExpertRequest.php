<?php

namespace Modules\User\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\User\Traits\CanFindUserWithBearerToken;

class ExpertRequest extends BaseFormRequest
{
    use CanFindUserWithBearerToken;

    public function rules()
    {
        if($this->favourites == 'true') {
          $user = $this->findUserWithBearerToken($this->header('Authorization'));
          $this->offsetSet('user', $user->id);
        }

        return [];
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
