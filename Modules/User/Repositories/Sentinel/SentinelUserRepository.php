<?php

namespace Modules\User\Repositories\Sentinel;

use File;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravolt\Avatar\Facade as Avatar;
use Modules\User\Events\UserIsCreating;
use Modules\User\Events\UserIsUpdating;
use Modules\User\Events\UserWasCreated;
use Modules\User\Events\UserWasUpdated;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Database\Eloquent\Builder;
use Modules\User\Events\UserHasMovedTeam;
use Modules\User\Enums\ApprovalStatusEnum;
use Modules\User\Enums\UserFileTypeEnum;

use Modules\User\Events\UserHasRegistered;
use Modules\User\Repositories\UserRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Modules\User\Exceptions\UserNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Exceptions\UserApprovalPendingException;
use Modules\User\Exceptions\UserApprovalRejectedException;
use Modules\User\Exceptions\UserHasBeenSuspendedException;
use Modules\Group\Services\GroupTagSyncer;
use Modules\Group\Repositories\Eloquent\EloquentGroupRepository;
use Modules\Group\Entities\Group;

class SentinelUserRepository implements UserRepository
{
    /**
     * @var \Modules\User\Entities\Sentinel\User
     */
    protected $user;
    /**
     * @var \Cartalyst\Sentinel\Roles\EloquentRole
     */
    protected $role;

    public function __construct()
    {
        $this->user = Sentinel::getUserRepository()->createModel();
        $this->role = Sentinel::getRoleRepository()->createModel();
    }

    /**
     * Returns all the users
     * @return object
     */
    public function all()
    {
        return $this->user->all();
    }

    /**
     * Create a user resource
     * @param  array $data
     * @param  bool $activated
     * @return mixed
     */
    public function create(array $data, $activated = false)
    {
        $data['unhashed_password'] = $data['password'];
        $this->hashPassword($data);

        event($event = new UserIsCreating($data));
        $user = $this->user->create($event->getAttributes());

        $user->saveAvatar($user->present()->fullname, null, true);

        if ($activated) {
            $this->activateUser($user);
            event(new UserWasCreated($user));
        } else {
            event(new UserHasRegistered($user, $data['unhashed_password']));
        }

        app(\Modules\User\Repositories\UserTokenRepository::class)->generateFor($user->id);

        return $user;
    }

    /**
     * Create a user resource
     * @param  array $data
     * @return mixed
     */
    public function createUser(array $data)
    {
        $this->hashPassword($data);

        $user = $this->user->create($data);

        $user->saveAvatar($user->present()->fullname, null, true);

        return $user;
    }

    /**
     * Create a user and assign roles to it
     * @param  array $data
     * @param  array $roles
     * @param bool $activated
     * @return User
     */
    public function createWithRoles($data, $roles, $activated = false)
    {
        $user = $this->create((array) $data, $activated);

        if (!empty($roles)) {
            $user->roles()->attach($roles);
        }

        return $user;
    }

    /**
     * Create a user and assign roles to it
     * But don't fire the user created event
     * @param array $data
     * @param array $roles
     * @param bool $activated
     * @return User
     */
    public function createWithRolesFromCli($data, $roles, $activated = false)
    {
        $this->hashPassword($data);
        $user = $this->user->create((array) $data);

        if (!empty($roles)) {
            $user->roles()->attach($roles);
        }

        if ($activated) {
            $this->activateUser($user);
        }

        return $user;
    }

    /**
     * Find a user by its ID
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->user->find($id);
    }

    /**
     * Update a user
     * @param $user
     * @param $data
     * @return mixed
     */
    public function update($user, $data)
    {
        $this->checkForNewPassword($data);

        if (isset($data['first_name']) && isset($data['last_name'])) {
            $user->saveAvatar(sprintf('%s %s', $data['first_name'], $data['last_name']), null);
        }

        if(isset($data['is_subscribed'])) {
            $data['digest_unsubscribed_at'] = !$data['is_subscribed'] ?  Carbon::now() : null;
            unset($data['is_subscribed']);
        }

        event($event = new UserIsUpdating($user, $data));

        $user->fill($event->getAttributes());
        $user->save();

        if (isset($data['company_location_id'])) {
            $user->companyLocations()->sync($data['company_location_id']);
        }

        if (isset($data['company_team_id'])) {
            $team = $user->companyTeams()->first();

            if (!$team || $team->id != $data['company_team_id']) {
                $user->companyTeams()->sync($data['company_team_id']);

                event(new UserHasMovedTeam($user, $team));
            }
        }

        if(isset($data['group_ids'])){
            $groupRepository = new EloquentGroupRepository(new Group());
            foreach ($data['group_ids'] as $key => $id) {
             $group = $groupRepository->find($id);

             if (!$group->is_joinable) {
                 unset($data['group_ids'][$key]);
              }
            }
            if($user->hasRoleSlug('trader')){
                $traderGroup = $groupRepository->findBySlug('trader');
                $data['group_ids'][] = $traderGroup->id;
                app(GroupTagSyncer::class)->setUser($user)->sync($data['group_ids']);
            }else{
                $expertGroup = $groupRepository->findBySlug('expert');
                $data['group_ids'][] = $expertGroup->id;
                $user->groups()->sync($data['group_ids']);
            }
        }

        if(isset($data['tag_ids']) && $user->hasRoleSlug('expert')){
            $user->tags()->sync($data['tag_ids']);
        }

        event(new UserWasUpdated($user));

        return $user;
    }

    /**
     * @param $userId
     * @param $data
     * @param $roles
     * @internal param $user
     * @return mixed
     */
    public function updateAndSyncRoles($userId, $data, $roles)
    {
        $user = $this->user->find($userId);

        $this->checkForNewPassword($data);

        $this->checkForManualActivation($user, $data);

        if (isset($data['first_name']) && isset($data['last_name'])) {
            $user->saveAvatar(sprintf('%s %s', $data['first_name'], $data['last_name']), isset($data['avatar']) ? $data['avatar'] : null);
        }

        event($event = new UserIsUpdating($user, $data));

        $user->fill($event->getAttributes());
        $user->save();

        event(new UserWasUpdated($user));

        if (!empty($roles)) {
            $user->roles()->sync(is_array($roles) ? $roles : [$roles]);
        }
    }

    /**
     * Deletes a user
     * @param $id
     * @throws UserNotFoundException
     * @return mixed
     */
    public function delete($id)
    {
        if ($user = $this->user->find($id)) {
            return User::where('id', $id)->delete();
        }

        throw new UserNotFoundException();
    }

    /**
     * Suspends a user
     * @param $id
     * @throws UserNotFoundException
     * @return mixed
     */
    public function suspend($id)
    {
        if ($user = $this->user->find($id)) {
            return $user->suspend();
        }

        throw new UserNotFoundException();
    }

    /**
     * Unsuspends a user
     * @param $id
     * @throws UserNotFoundException
     * @return mixed
     */
    public function unsuspend($id)
    {
        if ($user = $this->user->find($id)) {
            return $user->unsuspend();
        }

        throw new UserNotFoundException();
    }

    /**
     * Find a user by its credentials
     * @param  array $credentials
     * @return mixed
     */
    public function findByCredentials(array $credentials)
    {
        return Sentinel::findByCredentials($credentials);
    }

    /**
     * Find by email address
     * @param  string $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        return $this->user->where('email', $email)->first();
    }

    /**
     * Query all users by given role slugs
     * @param  array $slugs
     * @return mixed
     */
    public function queryByRoleSlugs($slugs = [])
    {
        $users = $this->user;

        if(count($slugs)) {
          $users = $users->whereHas('roles', function($query) use ($slugs) {
            $query->whereIn('slug', $slugs);
          });
        }

        return $users;
    }

    /**
     * Attempt to login.
     *
     * @param $user
     */
    public function attemptLogin($email, $password)
    {
        $user = $this->user->where('email', '=', $email)->first();

        if (!$user || !$user->passwordIs($password) || (!$user->hasRoleSlug('trader') && !$user->hasRoleSlug('expert'))) {
            throw new UserNotFoundException;
        }

        if ($user->approvalIsPending()) {
            throw new UserApprovalPendingException;
        }

        if ($user->approvalIsRejected()) {
            throw new UserApprovalRejectedException;
        }

        if ($user->isSuspended()) {
            throw new UserHasBeenSuspendedException;
        }

        $user->last_login = Carbon::now();
        $user->save();

        return $user;
    }

    /**
     * Paginating, ordering and searching through users
     * @param Array $meta
     * @param String $roleName
     * @return LengthAwarePaginator
     */
    public function filterAndPaginateUsing($meta, $roleSlugs = []) : LengthAwarePaginator
    {
        $users = $this->filterUsing($meta, $roleSlugs);


        if ($this->hasOrderByMeta($meta)) {
            ($meta['order_by'] == "status") and $meta['order_by'] = "suspended";
            $users->orderBy($meta['order_by'], $meta['order'] === 'ascending' ? 'asc' : 'desc');
        } else {
            $users->orderBy('created_at', 'desc');
        }

        return $users->paginate($this->hasMeta('per_page', $meta) ? $meta['per_page'] : 10)->appends($_GET);
    }

    /**
     * Filtering and searching through users
     * @param Array $meta
     * @param String $roleName
     * @return QueryBuilder
     */
    public function filterUsing($meta, $roleSlugs = [])
    {
        $users = $this->queryByRoleSlugs($roleSlugs);

        if (isset($meta['terms']) && $meta['terms']) {
            $users->whereHas('terms', function($query) {
                $query->where('user_files.type', UserFileTypeEnum::getType('TERMS_CONDITIONS'));
            });
        }

        if ($this->hasMeta('search', $meta)) {
            $term = $meta['search'];
            $users->where(function ($query) use ($term) {
                $query->where('first_name', 'LIKE', "%{$term}%")
                      ->orWhere('last_name', 'LIKE', "%{$term}%")
                      ->orWhere('email', 'LIKE', "%{$term}%")
                      ->orWhere('id', $term);
            });
        }

        if ($this->hasMeta('status', $meta)) {
            if ($meta['status'] == 'Active') {
                $users->whereHas('activations', function($query) {
                    $query->where('completed', true);
                });
                if(in_array('trader', $roleSlugs) || in_array('expert', $roleSlugs)) {
                  $users->where('suspended', false)->where('approval_status', ApprovalStatusEnum::ACCEPTED);
                }
            } else if ($meta['status'] == 'Pending') {
                $users->where('approval_status', ApprovalStatusEnum::PENDING);
            } else if ($meta['status'] == 'Not Activated') {
                $users->where(function($q) {
                    $q->whereHas('activations', function($query) {
                        $query->where('completed', false);
                    })->orWhereDoesntHave('activations');
                })->where('suspended', false);
            } else if ($meta['status'] == 'Suspended') {
                $users->where('suspended', true);
            } else if ($meta['status'] == 'Rejected') {
                $users->where('approval_status', ApprovalStatusEnum::REJECTED);
            }
        }

        if ($this->hasMeta('group', $meta)) {
            $group = $meta['group'];
            if ($group != 'All') {
                $users->whereHas('groups', function($query) use ($group) {
                    $query->where( DB::raw('group__groups.id'), $group);
                });
            }
        }

        if ($this->hasMeta('active_user', $meta)) {
            $users->exceptActiveUser($meta['active_user']);
        }

        if ($this->hasMeta('country', $meta) && !empty($meta['country'])) {
            $country = $meta['country'];
            if ($country != 'All') {
              $users->whereHas('companyCountry', function($query) use ($country) {
                $query->where('company__countries.code', $country);
              });
            }
        }

        if ($this->hasMeta('tags', $meta)) {
          $tags = $meta['tags'];
          $tags = json_decode($tags, true);
          if(is_array($tags) && count($tags)) {
            $users->whereHas('tags', function (Builder $query) use ($tags) {
              $query->whereHas('translations', function (Builder $query) use ($tags) {
                $query->whereIn('tag__tag_translations.slug', $tags);
              });
            });
          }
        }

        if (isset($meta['favourites']) && is_array($meta['favourites'])) {
            $users->whereIn('id', $meta['favourites']);
        }

        if ($this->hasMeta('users', $meta) && is_array($meta['users']) && count($meta['users'])) {
            $users->whereIn('id', $meta['users']);
        }

        return $users;
    }

    /**
     * Get all applicable users for digest emails
     * @param Array $meta
     * @param String $roleName
     * @return LengthAwarePaginator
     */
    public function getDigestable($meta, $roleGroup, $limit)
    {
        $users = $this->filterUsing($meta, $roleGroup)
                      ->where('digest_sent', 0)
                      ->whereNull('digest_unsubscribed_at');

        if ($this->hasOrderByMeta($meta)) {
            $users->orderBy($meta['order_by'], $meta['order'] === 'asc' ? 'asc' : 'desc');
        } else {
            $users->orderBy('created_at', 'desc');
        }

        if($limit) {
          $users = $users->limit($limit);
        }

        return $users->get();
    }

    /**
     * Reset all user digest sent emails
     * @param Array $meta
     * @param String $roleGroup
     * @return LengthAwarePaginator
     */
    public function getDigestSentUser($meta, $roleGroup, $limit)
    {
        $users = $this->filterUsing($meta, $roleGroup)
                      ->where('digest_sent', 1)
                      ->whereNull('digest_unsubscribed_at');

        if ($this->hasOrderByMeta($meta)) {
            $users->orderBy($meta['order_by'], $meta['order'] === 'asc' ? 'asc' : 'desc');
        } else {
            $users->orderBy('created_at', 'desc');
        }

        if($limit) {
          $users = $users->limit($limit);
        }

        return $users->get();
    }

    public function whereHasCompaniesAndGroups($companyIds = [], $groupIds = [])
    {
        return $this->queryByRoleSlugs(['user'])
                    ->whereHas('companies', function($query) use ($companyIds) {
                        $query->whereIn('company_id', $companyIds);
                    })
                    ->whereHas('groups', function($query) use ($groupIds) {
                        $query->whereIn('group_id', $groupIds);
                    });
    }

    /**
     * Hash the password key
     * @param array $data
     */
    private function hashPassword(array &$data)
    {
        $data['password'] = Hash::make($data['password']);
    }

    /**
     * Check if there is a new password given
     * If not, unset the password field
     * @param array $data
     */
    private function checkForNewPassword(array &$data)
    {
        if (array_key_exists('password', $data) === false) {
            return;
        }

        if ($data['password'] === '' || $data['password'] === null) {
            unset($data['password']);

            return;
        }

        $data['password'] = Hash::make($data['password']);
    }

    /**
     * Check and manually activate or remove activation for the user
     * @param $user
     * @param array $data
     */
    private function checkForManualActivation($user, array &$data)
    {
        if (!isset($data['activated'])) {
            return;
        }

        if (Activation::completed($user) && !$data['activated']) {
            return Activation::remove($user);
        }

        if (!Activation::completed($user) && $data['activated']) {
            $activation = Activation::create($user);

            return Activation::complete($user, $activation->code);
        }
    }

    /**
     * Activate a user automatically
     *
     * @param $user
     */
    public function activateUser($user)
    {
        $activation = Activation::create($user);
        Activation::complete($user, $activation->code);
    }

    /**
     * Checks if meta exists.
     * @return bool
     */
    private function hasMeta($metaKey, $meta) : bool
    {
        return isset($meta[$metaKey]) && !empty($meta[$metaKey]);
    }

    /**
     * Checks if order by meta exists.
     * @return bool
     */
    private function hasOrderByMeta($meta) : bool
    {
        return $this->hasMeta('order', $meta) && $this->hasMeta('order_by', $meta) && in_array($meta['order'], ['asc', 'desc', 'ascending', 'descending']);
    }

    public function updateTags($user, array $tags)
    {
        if (!array_key_exists('tags', $tags)) {
            return false;
        }

        $tagArray = [];
        foreach ($tags['tags'] as $tag) {
            $tagArray[$tag['id']] = ['enabled' => $tag['enabled']];
        }

        $user->tags()->syncWithoutDetaching($tagArray);
        return $user->save();
    }
}
