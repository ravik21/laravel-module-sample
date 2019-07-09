<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\User\Repositories\UserInviteRepository;
use Modules\Core\Http\Controllers\Api\BaseApiController;

class ExpertInviteController extends BaseApiController
{
    /**
     * @var UserInviteRepository
     */
    protected $userInvite;

    public function __construct(UserInviteRepository $userInvite)
    {
        parent::__construct();

        $this->userInvite = $userInvite;
    }

    /**
     * @SWG\Post(
     *     path="/experts/invite/check-code",
     *     summary="Validate Expert Invite Code.",
     *     tags={"Expert"},
     *     description="Check if Expert Invite Code is valid or expired.",
     *     operationId="expertInviteValidateCode",
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
     *         name="code",
     *         in="body",
     *         description="Expert Invite Code",
     *         required=true,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Please specify a ApiKey or Authorization (User Token).",
     *     )
     * )
     */
    public function index(Request $request)
    {
        $valid = $this->userInvite->isValidCode($request->get('code'));

        return $this->respond(['valid' => $valid]);
    }
}