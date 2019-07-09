<?php namespace Modules\User\Api\Transformers\Admin;

use Modules\User\Enums\ClientEnum;
use League\Fractal\TransformerAbstract;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Permissions\PermissionManager;

class UserTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['activation', 'company', 'group', 'location', 'team', 'tags'];

    /**
     * Transform User.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function transform(User $user)
    {
        $data = [
            'id'              => $user->id,
            'first_name'      => $user->first_name,
            'last_name'       => $user->last_name,
            'fullname'        => $user->present()->fullname,
            'email'           => $user->email,
            'avatar'          => $user->getAvatarUrl(),
            'activated'       => $user->isActivated(),
            'roles'           => $user->roles->pluck('id'),
            'role'            => $user->roles->first()->id,
            'role_name'       => $user->roles->first()->name,
            'suspended'       => $user->suspended ? true : false,
            'is_urgent'       => $user->is_urgent,
            'created_at'      => $user->created_at->format('H:i:s d/m/Y'),
            'last_login'      => $user->last_login ? $user->last_login->format('d/m/Y') : '',
            'has_web_client'  => $user->hasLoggedIntoClient(ClientEnum::WEB),
            'urls' => [
                'activate_url'  => route('api.user.admin.users.activate', $user->id),
                'delete_url'    => route('api.user.admin.users.destroy', $user->id),
                'suspend_url'   => route('api.user.admin.users.suspend', $user->id),
                'unsuspend_url' => route('api.user.admin.users.unsuspend', $user->id)
            ],
            'deleted_at'      => $user->deleted_at ? $user->deleted_at->format('Y/m/d') : ''
        ];

        $data['status'] = $user->getStatus();

        return $data;
    }

    /**
     * Include Activation.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function includeActivation(User $user)
    {
        if ($activation = $user->activations()->first()) {
            return $this->item($activation, new \Modules\User\Api\Transformers\Admin\ActivationTransformer);
        }

        return null;
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

    /**
     * Include Group.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function includeGroup(User $user)
    {
        if ($group = $user->groups->first()) {
            return $this->item($group, new \Modules\Group\Api\Transformers\GroupTransformer());
        }

        return null;
    }

    /**
     * Include Tags.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function includeTags(User $user)
    {
        return $this->collection($user->tags, new \Modules\Taggable\Api\Transformers\TagTransformer());
    }

    public function includePermissions(User $user)
    {
        $permissions = (new PermissionManager())->buildPermissionList($user);

        return $this->collection($permissions, function ($permission) {
           return $permission;
        });
    }


}
