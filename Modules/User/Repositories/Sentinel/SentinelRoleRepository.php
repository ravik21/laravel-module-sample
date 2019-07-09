<?php

namespace Modules\User\Repositories\Sentinel;

use Modules\User\Events\RoleIsCreating;
use Modules\User\Events\RoleIsUpdating;
use Modules\User\Events\RoleWasCreated;
use Modules\User\Events\RoleWasUpdated;
use Modules\User\Repositories\RoleRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SentinelRoleRepository implements RoleRepository
{
    /**
     * @var \Cartalyst\Sentinel\Roles\EloquentRole
     */
    protected $role;

    public function __construct()
    {
        $this->role = Sentinel::getRoleRepository()->createModel();
    }

    /**
     * Return all the roles
     * @return mixed
     */
    public function all()
    {
        return $this->role->all();
    }

    /**
     * Create a role resource
     * @return mixed
     */
    public function create($data)
    {
        event($event = new RoleIsCreating($data));
        $role = $this->role->create($event->getAttributes());

        event(new RoleWasCreated($role));

        return $role;
    }

    /**
     * Find a role by its id
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->role->find($id);
    }

    /**
     * Update a role
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data)
    {
        $role = $this->role->find($id);

        event($event = new RoleIsUpdating($role, $data));

        $role->fill($event->getAttributes());
        $role->save();

        event(new RoleWasUpdated($role));

        return $role;
    }

    /**
     * Delete a role
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $role = $this->role->find($id);

        return $role->delete();
    }

    /**
     * Find a role by its name(s).
     * @param  string $name
     * @return mixed
     */
    public function findByName($name)
    {
        return Sentinel::findRoleByName($name);
    }

    /**
     * Find a role by its name(s).
     * @param  string $name
     * @return mixed
     */
    public function getByMultipleNames($names = [])
    {
        return $this->role->whereIn('name', $names)->get();
    }

    /**
     * Paginating, ordering and searching through users
     *
     * @return LengthAwarePaginator
     */
    public function filterAndPaginateUsing($meta): LengthAwarePaginator
    {
        $roles = $this->role->newQuery();

        if ($this->hasMeta('search', $meta)) {
            $term = $meta['search'];
            $roles->where('name', 'LIKE', "%{$term}%")
                ->orWhere('slug', 'LIKE', "%{$term}%")
                ->orWhere('id', $term);
        }

        if ($this->hasOrderByMeta($meta)) {
            $users->orderBy($meta['order_by'], $meta['order'] === 'ascending' ? 'asc' : 'desc');
        } else {
            $roles->orderBy('created_at', 'desc');
        }

        return $roles->paginate($this->hasMeta('per_page', $meta) ? $meta['per_page'] : 10)->appends($_GET);
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
}
