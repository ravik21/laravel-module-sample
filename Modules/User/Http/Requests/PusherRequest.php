<?php

namespace Modules\User\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\User\Traits\CanFindUserWithBearerToken;

class PusherRequest extends BaseFormRequest
{
    use CanFindUserWithBearerToken;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->findUserWithBearerToken($this->header('Authorization'));
        $this->offsetSet('user', $user->id);

        return [];
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
}
