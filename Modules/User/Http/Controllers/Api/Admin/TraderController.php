<?php namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Enums\ApprovalStatusEnum;
use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserRepository;
use Modules\User\Events\UserHasBegunResetProcess;
use Modules\User\Events\UserApprovalHasBeenAccepted;
use Modules\User\Events\UserApprovalHasBeenRejected;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Api\Transformers\Admin\TraderTransformer;

class TraderController extends BaseApiController
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
        $meta  = $request->only('search', 'status', 'group', 'suspended', 'order_by', 'order', 'per_page', 'page');
        $users = $this->userRepo->filterAndPaginateUsing($meta, ['trader']);

        return $this->responder->parseIncludes(request()->get('includes', []))->collection($users->getCollection(), new TraderTransformer())->withPaginator($users)->get();
    }

    /**
     * Display a single resource.
     *
     * @param  User $user
     * @return Response
     */
    public function show(User $user)
    {
        if (!$user->hasRoleSlug('trader')) {
            return $this->respondNotFound(trans('user::messages.user not found'));
        }

        return $this->responder->item($user, new TraderTransformer())->get();
    }

    /**
     * Mark resource approval as accepted.
     *
     * @param  User $user
     * @return Response
     */
    public function markApprovalAsAccepted(User $user)
    {
        if (!$user->hasRoleSlug('trader')) {
            return $this->respondNotFound(trans('user::messages.user not found'));
        }

        $user->setApprovalStatus(ApprovalStatusEnum::ACCEPTED);

        event(new UserApprovalHasBeenAccepted($user));

        return $this->respond([
            'message' => trans('user::messages.user approval-accepted'),
            'status' => $user->getStatus(),
            'pending_approval' => $user->approvalIsAccepted()
        ]);
    }

    /**
     * Mark resource approval as rejected.
     *
     * @param  User $user
     * @return Response
     */
    public function markApprovalAsRejected(User $user)
    {
        if (!$user->hasRoleSlug('trader')) {
            return $this->respondNotFound(trans('user::messages.user not found'));
        }

        $user->setApprovalStatus(ApprovalStatusEnum::REJECTED);

        event(new UserApprovalHasBeenRejected($user));

        return $this->respond([
            'message' => trans('user::messages.user approval-rejected'),
            'status' => $user->getStatus(),
            'pending_approval' => $user->approvalIsRejected()
        ]);
    }
}
