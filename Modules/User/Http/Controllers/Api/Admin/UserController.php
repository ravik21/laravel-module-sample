<?php namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserRepository;
use Modules\User\Events\UserHasBegunResetProcess;
use Modules\User\Http\Requests\Admin\SendInviteRequest;
use Modules\User\Api\Transformers\Admin\UserTransformer;
use Modules\Core\Http\Controllers\Api\BaseApiController;

class UserController extends BaseApiController
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
        $users = $this->userRepo->filterAndPaginateUsing($meta, ['user']);

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
     * Begin password reset process.
     *
     * @param  User $user
     * @param  Authentication $auth
     * @return Response
     */
    public function sendResetPassword(User $user, Authentication $auth)
    {
        event(new UserHasBegunResetProcess($user, $auth->createReminderCode($user)));

        return $this->respond([
            'message' => trans('user::auth.reset password email was sent'),
        ]);
    }

    /**
     * Sends user an email invite.
     *
     * @param  SendInviteRequest $request
     * @return Response
     */
    public function sendInvite(SendInviteRequest $request)
    {
        $this->userRepo->sendInvite($request->email, $this->user, true);

         return $this->respond([
            'message' => trans('user::messages.user invite email was sent'),
        ]);
    }

    /**
     * Suspends the specified resource from storage.
     *
     * @param  User $user
     * @return Response
     */
    public function suspend(User $user)
    {
        if ($this->user->id == $user->id) {
            return $this->setStatusCode(409)->respondWithError(trans('user::messages.user is you'));
        }

        $user = $this->userRepo->suspend($user->id);

        return $this->respond([
            'message' => trans('user::messages.user suspended'),
            'suspended' => true,
            'status' => $user->getStatus()
        ]);
    }

    /**
     * Unsuspends the specified resource from storage.
     *
     * @param  User $user
     * @return Response
     */
    public function unsuspend(User $user)
    {
        if ($this->user->id == $user->id) {
            return $this->setStatusCode(409)->respondWithError(trans('user::messages.user is you'));
        }

        $user = $this->userRepo->unsuspend($user->id);

        return $this->respond([
            'message' => trans('user::messages.user unsuspended'),
            'suspended' => false,
            'status' => $user->getStatus()
        ]);
    }

    /**
     * Destroys the specified resource from storage.
     *
     * @param  User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        if ($this->user->id == $user->id) {
            return $this->setStatusCode(409)->respondWithError(trans('user::messages.user is you'));
        }

        $this->userRepo->delete($user->id);

        return $this->respond([
            'message' => trans('user::messages.user deleted')
        ]);
    }

    /**
     * Sets or Unsets the User as a Team Leader.
     *
     * @param  User $user
     * @return Response
     */
    public function toggleLeader(User $user)
    {
        $team = $user->companyTeams()->first();

        if ($team) {
            $team->pivot->is_leader = request()->get('is_leader', false);
            $team->pivot->save();
        }

        return $this->respond([
            'is_leader' => (int) $team->pivot->is_leader,
        ]);
    }

     /**
     * Activate the specified resource from storage.
     *
     * @param  User $user
     * @return Response
     */
    public function activate(User $user)
    {
        if ($user->isActivated()) {
            return $this->setStatusCode(409)->respondWithError(trans('user::messages.user already-activated'));
        }

        $user->activations()->delete();

        $this->userRepo->activateUser($user);

        return $this->respond([
            'message' => trans('user::messages.user activated'),
            'activated' => true,
            'status' => $user->getStatus()
        ]);
    }
}
