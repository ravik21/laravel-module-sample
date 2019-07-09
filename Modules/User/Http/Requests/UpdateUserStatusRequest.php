<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Traits\CanFindUserWithBearerToken;

class UpdateUserStatusRequest extends FormRequest
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
        $this->offsetSet('user_id', $user->id);

        return [
          'online_status' => 'required'
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
}
