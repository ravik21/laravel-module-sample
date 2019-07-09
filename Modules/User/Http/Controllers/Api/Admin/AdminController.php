<?php namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Repositories\UserRepository;
use Modules\User\Services\Admin\AdminRegistration;
use Modules\User\Http\Requests\Admin\CreateAdminRequest;
use Modules\User\Http\Requests\Admin\UpdateAdminRequest;
use Modules\User\Api\Transformers\Admin\UserTransformer;
use Modules\Core\Http\Controllers\Api\BaseApiController;

class AdminController extends BaseApiController
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * Constructor
     *
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
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
        $meta   = $request->only('search', 'status', 'suspended', 'order_by', 'order', 'per_page', 'page');
        $admins = $this->userRepo->filterAndPaginateUsing($meta, ['admin', 'super-admin']);

        return $this->responder->parseIncludes(request()->get('includes', []))->collection($admins->getCollection(), new UserTransformer())->withPaginator($admins)->get();
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
     * @param  CreateAdminRequest $request
     * @return Response
     */
    public function store(CreateAdminRequest $request)
    {
        $user = app(AdminRegistration::class)->register($request->all());

        return response()->json([
            'message' => trans('user::messages.user created'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  User $user
     * @param  UpdateAdminRequest $request
     * @return Response
     */
    public function update(User $user, UpdateAdminRequest $request)
    {
        $this->userRepo->updateAndSyncRoles($user->id, $request->only('first_name', 'last_name', 'email', 'password', 'password_confirmation'), $request->get('role'));

        return response()->json([
            'message' => trans('user::messages.user updated'),
        ]);
    }
}
