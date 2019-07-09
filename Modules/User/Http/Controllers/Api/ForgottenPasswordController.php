<?php namespace Modules\User\Http\Controllers\Api;

use Modules\User\Repositories\UserRepository;
use Modules\User\Services\AdminOrUserResetter;
use Modules\User\Exceptions\UserNotFoundException;
use Modules\User\Http\Requests\ForgottenPasswordRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;

class ForgottenPasswordController extends BaseApiController
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var AdminOrUserResetter
     */
    private $userResetter;

    /**
     * Constructor
     *
     * @param UserRepository $userRepo
     * @param AdminOrUserResetter $userResetter
     */
    public function __construct(UserRepository $userRepo, AdminOrUserResetter $userResetter)
    {
        $this->userRepo     = $userRepo;
        $this->userResetter = $userResetter;

        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/users/forgotten-password",
     *     summary="Request password reset.",
     *     tags={"User"},
     *     description="Sends password reset email if valid email address provided.",
     *     operationId="userForgottenPassword",
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
     *         description="Valid Email Address that exists in system.",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                   property="email",
     *                   type="string",
     *             )
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
    public function store(ForgottenPasswordRequest $request)
    {
        $this->userResetter->startReset($request->only('email'));

        return response()->json(["message" => trans('user::messages.check email to reset password')], 200);
    }
}
