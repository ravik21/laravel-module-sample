<?php

namespace Modules\User\Api\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\User\Entities\Sentinel\User;

class ProfileTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['company', 'location', 'team'];

    public function __construct($user)
    {
        $this->user = $user;
    }

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
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->present()->fullname,
            'avatar' => $user->getAvatarUrl(),
            'has_custom_avatar' => $user->hasCustomAvatar()
        ];
    }

    /**
     * Include Company.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function includeCompany(User $user)
    {
        if ($company = $user->companies->first()) {
            return $this->item($company, new \Modules\Company\Api\Transformers\CompanyTransformer);
        }

        return null;
    }

    /**
     * Include Company Location.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function includeLocation(User $user)
    {
        if ($location = $user->companyLocations->first()) {
            return $this->item($location, new \Modules\Company\Api\Transformers\LocationTransformer);
        }

        return null;
    }

    /**
     * Include Company Team.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function includeTeam(User $user)
    {
        if ($team = $user->companyTeams->first()) {
            return $this->item($team, new \Modules\Company\Api\Transformers\TeamTransformer);
        }

        return null;
    }
}
