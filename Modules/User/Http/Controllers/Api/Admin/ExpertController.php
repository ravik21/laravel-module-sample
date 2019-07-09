<?php namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Enums\ApprovalStatusEnum;
use Modules\User\Repositories\UserRepository;
use Modules\User\Events\UserApprovalHasBeenAccepted;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Http\Requests\UpdateProfileRequest;
use Modules\User\Api\Transformers\Admin\ExpertTransformer;
use Modules\User\Http\Requests\Admin\SendExpertInviteRequest;
use Modules\User\Events\UserApprovalHasBeenRejected;

class ExpertController extends BaseApiController
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
        $users = $this->userRepo->filterAndPaginateUsing($meta, ['expert']);

        return $this->responder->parseIncludes(request()->get('includes', []))->collection($users->getCollection(), new ExpertTransformer())->withPaginator($users)->get();
    }

    /**
     * Display a single resource.
     *
     * @param  User $user
     * @return Response
     */
    public function show(User $user)
    {
        if (!$user->hasRoleSlug('expert')) {
            return $this->respondNotFound(trans('user::messages.user not found'));
        }

        return $this->responder->item($user, new ExpertTransformer())->get();
    }

    /**
     * Display a single resource.
     *
     * @param  User $user
     * @return Response
     */
    public function invite(SendExpertInviteRequest $request)
    {
        $inviteDetails = $request->only('recipients', 'message');

        \Log::error($inviteDetails);

        return $this->respond([
            'message' => trans('user::messages.user invite email was sent')
        ]);
    }

    /**
     * Mark resource approval as accepted.
     *
     * @param  User $user
     * @return Response
     */
    public function markApprovalAsAccepted(User $user)
    {
        if (!$user->hasRoleSlug('expert')) {
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
        if (!$user->hasRoleSlug('expert')) {
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

    public function update(UpdateProfileRequest $request)
    {
        $profileDetails = $request->only('first_name', 'last_name', 'gender', 'password', 'company_location_id', 'company_team_id','title','company_name','company_number','parent_company_name','company_vat_no','company_phone_contact','company_position','company_street','company_town','company_region','company_postcode','group_ids','tag_ids','company_country_id','timezone','hour_rate','day_rate','languages','availability','professional_experience','past_projects','education','memberships','references', 'is_subscribed');

        $user       = $this->userRepo->find($request->id);
        $user       = $this->userRepo->update($user, $profileDetails);
        $apiToken   = $user->getLastApiToken();

        return $this->responder->item($user, new ExpertTransformer($apiToken))->get();
    }
}
