<?php namespace Modules\User\Api\Transformers;

use Modules\User\Enums\ClientEnum;
use League\Fractal\TransformerAbstract;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Api\Transformers\FileTransformer;

class ExpertTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['country', 'groups', 'tags', 'permissions', 'terms'];

    /**
     * @var token
     */
    protected $token;

    /**
     * @var clientId
     */
    protected $clientId;

    public function __construct($token = null)
    {
        $this->token = $token;
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
            'region'                => $user->company_region,
            'company_position'      => $user->company_position,
            'company_name'          => $user->company_name,
            'token'                 => $this->token ?: null,
            'timezone'              => $user->timezone,
            'roles'                 => $user->roles->pluck('slug'),
            'hour_rate'             => $user->hour_rate,
            'day_rate'              => $user->day_rate,
            'experience'            => $user->professional_experience,
            'past_projects'         => $user->past_projects,
            'education'             => $user->education,
            'memberships'           => $user->memberships,
            'availability'          => $user->availability,
            'languages'             => $user->languages,
            'references'            => $user->references,
            'avatar'                => $user->getAvatarUrl(),
            'has_custom_avatar'     => $user->hasCustomAvatar(),
            'online_status'         => $user->online_status,
            'activated'             => $user->isActivated(),
            'intro_viewed'          => $user->clientIntroViewed($this->clientId),
            'has_web_client'        => $user->hasLoggedIntoClient(ClientEnum::WEB),
            'company_number'        => $user->company_number,
            'parent_company_name'   => $user->parent_company_name,
            'company_vat_no'        => $user->company_vat_no,
            'company_phone_contact' => $user->company_phone_contact,
            'company_street'        => $user->company_street,
            'company_town'          => $user->company_town,
            'company_postcode'      => $user->company_postcode,
            'company_country_id'    => $user->company_country_id,
            'is_subscribed'         => $user->digest_unsubscribed_at ? false : true,
            'deleted_at'            => $user->deleted_at ? $user->deleted_at->format('Y/m/d') : ''
        ];
    }

    /**
     * Include Company Country.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function includeCountry(User $user)
    {
        if ($user->companyCountry()->exists()) {
            return $this->item($user->companyCountry, new \Modules\Company\Api\Transformers\CountryTransformer);
        }

        return null;
    }

    /**
     * Include Groups.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
    public function includeGroups(User $user)
    {
        return $this->collection($user->openGroups, new \Modules\Group\Api\Transformers\GroupTransformer);
    }

    /**
     * Include Tags.
     *
     * @param User $user
     * @return League\Fractal\ItemResource
     */
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

    public function includeTerms(User $user)
    {
        $termsConditions = $user->termsConditions();
        return $this->collection($termsConditions->get(), new FileTransformer());
    }
}
