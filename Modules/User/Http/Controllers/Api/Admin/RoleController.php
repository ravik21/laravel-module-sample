<?php

namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Permissions\PermissionManager;
use Cartalyst\Sentinel\Roles\EloquentRole as Role;
use Modules\User\Http\Requests\Admin\CreateRoleRequest;
use Modules\User\Http\Requests\Admin\UpdateRoleRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Api\Transformers\Admin\RoleTransformer;

class RoleController extends BaseApiController
{
    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * @var PermissionManager
     */
    protected $permissions;

    public function __construct(RoleRepository $role, PermissionManager $permissions)
    {
        $this->role = $role;
        $this->permissions = $permissions;

        parent::__construct();
    }

    public function index(Request $request)
    {
        $meta  = $request->only('search', 'order_by', 'order', 'per_page', 'page');
        $roles = $this->role->filterAndPaginateUsing($meta);

        return $this->responder->parseIncludes(request()->get('includes', []))->collection($roles->getCollection(), new RoleTransformer())->withPaginator($roles)->get();
    }

    public function show(Role $role)
    {
        return $this->responder->item($role->load('users'), new RoleTransformer());
    }

    public function store(CreateRoleRequest $request)
    {
        $this->role->create($request->all());

        return response()->json([
            'message' => trans('core::core.messages.resource created', ['name' => trans('user::roles.title.role')])
        ]);
    }
    public function update(Role $role, UpdateRoleRequest $request)
    {
        $request = $this->mergeRequestWithPermissions($request);

        $this->role->update($role->id, $request);

        return response()->json([
            'message' => trans('core::core.messages.resource updated', ['name' => trans('user::roles.title.role')])
        ]);
    }

    public function destroy(Role $role)
    {
        $this->role->delete($role->id);

        return response()->json([
            'message' => trans('user::messages.role deleted'),
        ]);
    }

    public function getAdminRoles()
    {
        $roles = $this->role->getByMultipleNames(['Admin', 'Super Admin']);

        return $this->responder->collection($roles, new RoleTransformer())->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    private function mergeRequestWithPermissions(Request $request)
    {
        $permissions = $this->permissions->clean($request->get('permissions'));

        return array_merge($request->all(), ['permissions' => $permissions]);
    }
}
