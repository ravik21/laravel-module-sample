<?php

namespace Modules\User\Services\Admin;

use Modules\User\Contracts\Authentication;
use Modules\User\Events\AdminHasRegistered;
use Modules\User\Repositories\UserRepository;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserClientRepository;

class AdminRegistration
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
     * @var array
     */
    private $input;

    public function __construct(Authentication $auth, UserRepository $user, UserClientRepository $client, RoleRepository $role)
    {
        $this->auth   = $auth;
        $this->user   = $user;
        $this->role   = $role;
        $this->client = $client;
    }

    /**
     * @param array $input
     * @return mixed
     */
    public function register(array $input)
    {
        $this->input = $input;

        if ($this->isInvite()) {
            unset($this->input['password']);
            unset($this->input['password_confirmation']);
        }

        $user = $this->createUser();

        if ($this->hasProfileData()) {
            $this->createProfileForUser($user);
        }

        $this->assignUserToGroup($user);

        $this->assignUserToClient($user);

        event(new AdminHasRegistered($user, $this->isInvite() ? '' : $this->input['password'], $this->isInvite()));

        return $user;
    }

    private function createUser()
    {
        $user = $this->auth->register((array) $this->input);

        $user->saveAvatar($user->present()->fullname, null, true);

        return $user;
    }

    private function assignUserToGroup($user)
    {
        $role = $this->role->find($this->input['role']);

        $this->auth->assignRole($user, $role);
    }

    /**
     * Check if the request input has a profile key
     * @return bool
     */
    private function hasProfileData()
    {
        return isset($this->input['profile']);
    }

    /**
     * Create a profile for the given user
     * @param $user
     */
    private function createProfileForUser($user)
    {
        $profileData = array_merge($this->input['profile'], ['user_id' => $user->id]);
        app('Modules\Profile\Repositories\ProfileRepository')->create($profileData);
    }

    /**
     * Determins if registration was via another user invitation.
     * @return bool
     */
    private function isInvite()
    {
        return isset($this->input['add_type']) && $this->input['add_type'] === 'invite';
    }

    private function assignUserToClient($user)
    {
        $this->client->findOrCreateForUser($this->input['client_id'], $user->id);
    }
}
