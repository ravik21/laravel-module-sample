<?php namespace Modules\User\Api\Transformers\Admin;

use Modules\User\Enums\ClientEnum;
use League\Fractal\TransformerAbstract;
use Modules\User\Entities\Sentinel\User;

class ExpertTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['activation', 'company', 'groups', 'location', 'team', 'tags', 'country'];

    /**
     * Transform User.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function transform(User $user)
    {
        $rating = $user->bookings()->averageRating()->first();

        $data = [
            'id'                    => $user->id,
            'first_name'            => $user->first_name,
            'last_name'             => $user->last_name,
            'fullname'              => $user->present()->fullname,
            'full_name'             => $user->present()->fullname,
            'email'                 => $user->email,
            'avatar'                => $user->getAvatarUrl(),
            'activated'             => $user->isActivated(),
            'invited'               => $user->isInvited(),
            'roles'                 => $user->roles->pluck('id'),
            'role'                  => $user->roles->first()->id,
            'is_urgent'             => $user->is_urgent,
            'role_name'             => $user->roles->first()->name,
            'suspended'             => $user->suspended ? true : false,
            'created_at'            => $user->created_at->format('H:i:s d/m/Y'),
            'last_login'            => $user->lastLoggedIntoClient(ClientEnum::WEB),
            'has_web_client'        => $user->hasLoggedIntoClient(ClientEnum::WEB),
            'pending_approval'      => $user->approvalIsPending(),
            'vat_number'            => $user->company_vat_no,

            'company_number'        => $user->company_number,
            'parent_company_name'   => $user->parent_company_name,
            'company_vat_no'        => $user->company_vat_no,
            'company_phone_contact' => $user->company_phone_contact,
            'company_street'        => $user->company_street,
            'company_town'          => $user->company_town,
            'company_country_id'    => $user->company_country_id,

            'hour_rate'             => $user->hour_rate,
            'day_rate'              => $user->day_rate,
            'experience'            => $user->professional_experience,
            'past_projects'         => $user->past_projects,
            'education'             => $user->education,
            'memberships'           => $user->memberships,
            'availability'          => $user->availability,
            'languages'             => $user->languages,
            'references'            => $user->references,

            'company_position'      => $user->company_position,
            'address'               => $user->address(),
            'postcode'              => $user->company_postcode,
            'region'                => $user->company_region,
            'vat_number_registered' => $user->company_vat_registered,
            'timezone'              =>  $user->timezone,
            'urls' => [
                'activate_url'        => route('api.user.admin.users.activate', $user->id),
                'delete_url'          => route('api.user.admin.users.destroy', $user->id),
                'suspend_url'         => route('api.user.admin.users.suspend', $user->id),
                'unsuspend_url'       => route('api.user.admin.users.unsuspend', $user->id),
                'accept_approval_url' => route('api.user.admin.experts.accept', $user->id),
                'reject_approval_url' => route('api.user.admin.experts.reject', $user->id)
            ],
            'deleted_at'            => $user->deleted_at ? $user->deleted_at->format('Y/m/d') : '',
            'average_rating'        => $rating && $rating->average_rating ? number_format($rating->average_rating, 2) : null
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
    public function includeGroups(User $user)
    {
        return $this->collection($user->groups, new \Modules\Group\Api\Transformers\GroupTransformer());
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

    public function includeCountry(User $user)
    {
        if ($user->companyCountry()->exists()) {
            return $this->item($user->companyCountry, new \Modules\Company\Api\Transformers\CountryTransformer);
        }

        return null;
    }
}
