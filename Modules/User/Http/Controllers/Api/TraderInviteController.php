<?php

namespace Modules\User\Http\Controllers\Api;

use Modules\User\Events\TraderIsInviting;
use Modules\User\Http\Requests\SendTraderInviteRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;

class TraderInviteController extends BaseApiController
{
    /**
     * @SWG\Post(
     *     path="/traders/invite",
     *     summary="Send invite to email address provided.",
     *     tags={"Trader"},
     *     description="Send an invite to a non-registered email address.",
     *     operationId="traderSendInvite",
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
     *         name="email",
     *         in="body",
     *         description="Email",
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
     *     ),
    *      @SWG\Response(
     *         response="401",
     *         description="Invalid ApiKey or Authorization (User Token).",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.",
     *     )
     * )
     */
    public function store(SendTraderInviteRequest $request)
    {
        event(new TraderIsInviting($request->email, $this->user));

         return $this->respond([
            'message' => trans('user::messages.trader invite email was sent'),
        ]);
    }
}