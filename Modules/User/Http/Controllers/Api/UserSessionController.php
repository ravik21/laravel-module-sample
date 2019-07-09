<?php

namespace Modules\User\Http\Controllers\Api;

use Modules\User\Enums\ClientEnum;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Requests\UpdateUserStatusRequest;
use Modules\User\Http\Requests\LogoutRequest;
use Modules\User\Http\Requests\PusherRequest;
use Modules\User\Repositories\UserRepository;
use Modules\User\Exceptions\UserNotFoundException;
use Modules\User\Repositories\UserClientRepository;
use Modules\User\Repositories\UserTokenRepository;
use Modules\User\Api\Transformers\TraderTransformer;
use Modules\User\Api\Transformers\ExpertTransformer;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Exceptions\UserApprovalPendingException;
use Modules\User\Exceptions\UserApprovalRejectedException;
use Modules\User\Exceptions\UserHasBeenSuspendedException;
use Modules\User\Api\Transformers\UnverifiedUserTransformer;
use Pusher\Pusher;
use Modules\User\Broadcast\BroadcastService;

class UserSessionController extends BaseApiController
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var UserClientRepository
     */
    private $userClientRepo;

    /**
     * @var UserTokenRepository
     */
    private $userTokenRepo;

    /**
     * Constructor
     *
     * @param UserRepository $userRepo
     * @param UserClientRepository $userClientRepo
     * @param UserTokenRepository $userTokenRepo
     */
    public function __construct(UserRepository $userRepo, UserClientRepository $userClientRepo, UserTokenRepository $userTokenRepo)
    {
        $this->userRepo       = $userRepo;
        $this->userClientRepo = $userClientRepo;
        $this->userTokenRepo  = $userTokenRepo;

        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/users/login",
     *     summary="Login with credentials.",
     *     tags={"User"},
     *     description="Permits an Authorization attempt.",
     *     operationId="userLogin",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="ApiKey",
     *         in="header",
     *         description="Valid Api Key for this API. ",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Email and Password of User and Client currently being used.",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                   property="email",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="password",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="client_id",
     *                   type="integer",
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Please specify an ApiKey.",
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Invalid ApiKey or Credentials supplied.",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.",
     *     )
     * )
     */
    public function store(LoginRequest $request)
    {
        try
        {
            $credentials = $request->only('email', 'password');

            $user = $this->userRepo->attemptLogin($credentials['email'], $credentials['password']);

            if (!$user->isActivated()) {
                return $this->responder->item($user, new UnverifiedUserTransformer())->get();
            }

            $this->userClientRepo->loggedIn(ClientEnum::WEB, $user);

            $userToken = $this->userToken->generateFor($user->id);
        } catch (UserNotFoundException $exception) {
            return $this->setStatusCode(401)->respondWithError(trans('user::users.invalid login or password'));
        } catch (UserApprovalPendingException $exception) {
            return $this->setStatusCode(403)->respondWithError(trans('user::users.account approval is pending'));
        } catch (UserApprovalRejectedException $exception) {
            return $this->setStatusCode(403)->respondWithError(trans('user::users.account approval is rejected'));
        } catch (UserHasBeenSuspendedException $exception) {
            return $this->setStatusCode(403)->respondWithError(trans('user::users.account is suspended'));
        }

        if ($user->hasRoleSlug('trader')) {
            return $this->responder->item($user, new TraderTransformer($userToken->access_token, ClientEnum::WEB))->get();
        } else {
            return $this->responder->item($user, new ExpertTransformer($userToken->access_token, ClientEnum::WEB))->get();
        }
    }

    /**
     * @SWG\Post(
     *     path="/users/validate-token",
     *     summary="Validates a UserToken.",
     *     tags={"User"},
     *     description="Determines if token is valid.",
     *     operationId="userValidateToken",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="ApiKey",
     *         in="header",
     *         description="Valid Api Key for this API. ",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="User Token to validate.",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                   property="user_token",
     *                   type="string",
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Please specify an ApiKey.",
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Invalid ApiKey or Credentials supplied.",
     *     ),
     * )
     */
    public function validate()
    {
        $accessToken = request()->only('user_token');

        if ($this->userTokenRepo->hasExpired($accessToken)) {
            return $this->setStatusCode(401)->respondWithError('Invalid User Token');
        }

        $userToken = $this->userTokenRepo->findByAttributes(['access_token' => $accessToken]);

        if ($user->hasRoleSlug('trader')) {
            return $this->responder->item($user, new TraderTransformer($userToken->access_token))->get();
        } else {
            return $this->responder->item($user, new ExpertTransformer($userToken->access_token))->get();
        }
    }

    public function authenticatePusher(PusherRequest $request)
    {
        return app(BroadcastService::class)->authenticate($request);
    }

    public function updateStatus(UpdateUserStatusRequest $request)
    {
        $user = $this->userRepo->find($request->user_id);
        $this->userRepo->update($user, ['online_status' => $request->online_status]);

        return app(BroadcastService::class)->broadcast(['alegrant-channel'], 'user-status-update', [
          'online_status' => $request->online_status,
          'user'          => $request->user_id
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/users/logout",
     *     summary="Destroys a UserToken.",
     *     tags={"User"},
     *     description="Destroys the given UserToken.",
     *     operationId="userDestroyToken",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="ApiKey",
     *         in="header",
     *         description="Valid Api Key for this API. ",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *     ),
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="Valid User Token for logged in User.",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="User Token to validate.",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                   property="user_token",
     *                   type="string",
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Please specify an ApiKey.",
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Invalid ApiKey or Credentials supplied.",
     *     ),
     * )
     */
    public function destroy(LogoutRequest $request)
    {
        $token = $this->userTokenRepo->findByAttributes(['access_token' => $request->only('user_token')]);

        if ($token) {
            $this->userTokenRepo->destroy($token);
        }

        return response()->json([
            'message' => trans('core::core.messages.resource deleted', ['name' => trans('user::users.title.user-token')])
        ]);
    }
}
