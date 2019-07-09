<?php

namespace Modules\User\Services\Expert;

use Modules\User\Enums\ClientEnum;
use Modules\User\Enums\ApprovalStatusEnum;
use Modules\User\Contracts\Authentication;
use Modules\User\Events\UserHasRegistered;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Modules\Group\Repositories\GroupRepository;
use Modules\Company\Repositories\CompanyRepository;
use Modules\User\Repositories\UserClientRepository;

class ExpertRegistration
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var UserClientRepository
     */
    protected $client;

    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * @var CompanyRepository
     */
    protected $company;

    /**
     * @var GroupRepository
     */
    protected $group;

    /**
     * @var array
     */
    protected $input;

    public function __construct(Authentication $auth, UserRepository $user, UserClientRepository $client, RoleRepository $role, CompanyRepository $company, GroupRepository $group)
    {
        $this->auth    = $auth;
        $this->user    = $user;
        $this->role    = $role;
        $this->client  = $client;
        $this->company = $company;
        $this->group   = $group;
    }

    /**
     * @param array $input
     * @return mixed
     */
    public function register(array $input)
    {
        $this->input = $input;

        $user = $this->createUser();

        $this->assignUserToRole($user);
        $this->assignUserToCompany($user);
        $this->assignUserToClient($user);
        $this->assignUserToGroupsAndTags($user);

        $tags = [];

        foreach ($this->input['tag_ids'] as $key => $tagId) {
          $tags['tags'][] = [
            'id' => $tagId,
            'enabled' => 1
          ];
        }

        $this->user->updateTags($user, $tags);

        event(new UserHasRegistered($user, null));

        return $user;
    }

    private function createUser()
    {
        $userDetails = (array) $this->input;

        $user = $this->user->createUser($userDetails, false);

        $user->setApprovalStatus(ApprovalStatusEnum::PENDING);

        $invite = $user->invites()->where('code', $this->input['invite_code'])->first();

        if ($invite) {
            $invite->markAsComplete();
        }

        return $user;
    }

    private function assignUserToRole($user)
    {
        $role = $this->role->findByName('expert');

        $this->auth->assignRole($user, $role);
    }

    private function assignUserToCompany($user)
    {
        $input = (array) $this->input;

        $company = $this->company->findByCode('ALEGRANT2018');
        $user->companies()->save($company, ['company_code' => 'ALEGRANT2018']);

        $user->save();
    }

    private function assignUserToClient($user)
    {
        $this->client->findOrCreateForUser(ClientEnum::WEB, $user->id);
    }

    private function assignUserToGroupsAndTags($user)
    {
        foreach ($this->input['group_ids'] as $key => $id) {
            $group = $this->group->find($id);

            if (!$group->is_joinable) {
                unset($this->input['group_ids'][$key]);
            }
        }

        $expertGroup = $this->group->findBySlug('expert');
        $this->input['group_ids'][] = $expertGroup->id;

        $user->groups()->sync($this->input['group_ids']);
        $user->tags()->sync($this->input['tag_ids']);
    }
}
