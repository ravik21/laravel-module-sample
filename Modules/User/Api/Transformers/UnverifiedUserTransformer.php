<?php namespace Modules\User\Api\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\User\Entities\Sentinel\User;

class UnverifiedUserTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [];

    /**
     * Transform User.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'activated' => $user->isActivated(),
            'activated_message' => trans('user::users.account not validated')
        ];
    }
}