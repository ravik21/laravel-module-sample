<?php namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Repositories\UserRepository;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Http\Requests\Admin\CreateUserRequest;
use Modules\User\Http\Requests\Admin\UpdateUserRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Api\Transformers\Admin\UserTransformer;

class MainController extends BaseApiController
{
    /**
     * @var PermissionManager
     */
    protected $permissions;

    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * Constructor
     *
     * @param PermissionManager $permissions
     * @param UserRepository $userRepo
     */
    public function __construct(PermissionManager $permissions, UserRepository $userRepo)
    {
        $this->permissions = $permissions;
        $this->userRepo = $userRepo;

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $meta  = $request->only('search', 'status', 'suspended', 'order_by', 'order', 'per_page', 'page');
        $users = $this->userRepo->filterAndPaginateUsing($meta, ['super-admin', 'super-admin', 'admin', 'expert', 'trader','user']);

        return $this->responder->parseIncludes(request()->get('includes', []))->collection($users->getCollection(), new UserTransformer())->withPaginator($users)->get();
    }

    /**
     * Display a single resource.
     *
     * @param  User $user
     * @return Response
     */
    public function show(User $user)
    {
        return $this->responder->item($user, new UserTransformer())->get();
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  CreateUserRequest $request
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);

        $this->userRepo->createWithRoles($data, $request->roles, true);

        return response()->json([
            'message' => trans('user::messages.user created'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  User $user
     * @param  UpdateUserRequest $request
     * @return Response
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);

        $this->userRepo->updateAndSyncRoles($user->id, $data, $request->roles);

        return response()->json([
            'message' => trans('user::messages.user updated'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Integer $id
     * @param  UpdateUserRequest $request
     * @return Response
     */
    protected function mergeRequestWithPermissions(Request $request)
    {
        $permissions = $this->permissions->clean($request->permissions);

        return array_merge($request->all(), ['permissions' => $permissions]);
    }

    public function permissions()
    {
        $user   = $this->user;
        return $this->responder->item($user->secondaryPermissions(), function ($permission) {
          return $permission;
        })->get();
    }
}
