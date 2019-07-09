<?php

namespace Modules\User\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\User\Traits\CanFindUserWithBearerToken;

class MarkAsFavouriteRequest extends BaseFormRequest
{
    use CanFindUserWithBearerToken;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $trader   = $this->findUserWithBearerToken($this->header('Authorization'));
        $this->offsetSet('trader', $trader->id);

        return [
            'experts' => 'required|array',
        ];
    }
}
