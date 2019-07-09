<?php namespace Modules\User\Api\Transformers;

use Modules\User\Enums\ClientEnum;
use League\Fractal\TransformerAbstract;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Api\Transformers\FileTransformer;

class UserTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['company', 'location', 'team','groups','tags', 'permissions', 'country', 'terms'];

    /**
     * @var token
     */
    protected $token;

    /**
     * @var clientId
     */
    protected $clientId;

    public function __construct($token = null, $clientId = null)
    {
        $this->token = $token;
        $this->clientId = $clientId;
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
            'id'                    => $user->id,
            'email'                 => $user->email,
            'title'                 => $user->title,
            'gender'                => $user->gender,
            'first_name'            => $user->first_name,
            'last_name'             => $user->last_name,
            'full_name'             => $user->present()->fullname,
            'token'                 => $this->token ?: null,
            'roles'                 => $user->roles->pluck('slug'),
            'avatar'                => $user->getAvatarUrl(),
            'has_custom_avatar'     => $user->hasCustomAvatar(),
            'activated'             => $user->isActivated(),
            'intro_viewed'          => $user->clientIntroViewed($this->clientId),
            'has_web_client'        => $user->hasLoggedIntoClient(ClientEnum::WEB),
            'company_name'          => $user->company_name,
            'hour_rate'             => $user->hour_rate,
            'day_rate'              => $user->day_rate,
            'company_number'        => $user->company_number,
            'timezone'              => $user->timezone,
            'parent_company_name'   => $user->parent_company_name,
            'company_vat_no'        => $user->company_vat_no,
            'company_phone_contact' => $user->company_phone_contact,
            'company_position'      => $user->company_position,
            'company_street'        => $user->company_street,
            'company_town'          => $user->company_town,
            'company_region'        => $user->company_region,
            'company_postcode'      => $user->company_postcode,
            'company_country_id'    => $user->company_country_id,
            'region'                => $user->company_region,
            'online_status'         => $user->online_status,
            'deleted_at'            => $user->deleted_at ? $user->deleted_at->format('Y/m/d') : ''
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

    public function includeGroups(User $user)
    {
        return $this->collection($user->openGroups, new \Modules\Group\Api\Transformers\GroupTransformer);
    }

    public function includeTags(User $user)
    {
        $tags = $user->tags()->join('tag__tag_translations', 'tag__tag_translations.tag_id', '=', 'tag__tags.id')
                   ->select('tag__tags.*')
                   ->where('locale', 'en')
                   ->orderBy('name');

        return $this->collection($tags->get(), new \Modules\User\Api\Transformers\TagTransformer());
    }

    public function includePermissions(User $user)
    {
        return $this->collection($user->secondaryPermissions(), function ($permission) {
           return $permission;
        });
    }

    public function includeCountry(User $user)
    {
        if ($user->companyCountry()->exists()) {
            return $this->item($user->companyCountry, new \Modules\Company\Api\Transformers\CountryTransformer);
        }

        return null;
    }

    public function includeTerms(User $user)
    {
        $termsConditions = $user->termsConditions();
        return $this->collection($termsConditions->get(), new FileTransformer());
    }
}
