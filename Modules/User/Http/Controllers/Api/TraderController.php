<?php

namespace Modules\User\Http\Controllers\Api;

use Modules\User\Services\Trader\TraderRegistration;
use Modules\User\Http\Requests\TraderRegisterRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Api\Transformers\UnverifiedUserTransformer;
use Modules\User\Repositories\UserRepository;
use Modules\User\Api\Transformers\TraderTransformer;

class TraderController extends BaseApiController
{
    /**
     * @var TraderRegistration
     */
    private $traderRegistration;

    /**
     * Constructor
     *
     * @param TraderRegistration $traderRegistration
     */
    public function __construct(TraderRegistration $traderRegistration)
    {
        $this->traderRegistration = $traderRegistration;

        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/traders/register",
     *     summary="Register a Trader.",
     *     tags={"Trader"},
     *     description="Permits a Registration attempt.",
     *     operationId="traderRegister",
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
     *         description="Registration Details for User.",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                   property="first_name",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="last_name",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="email",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="password",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="company_name",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="company_country_id",
     *                   type="integer",
     *             ),
     *             @SWG\Property(
     *                   property="group_ids",
     *                   type="array",
     *                   @SWG\Items(
     *                      type="integer"
     *                   )
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
     *         description="Invalid ApiKey supplied.",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.",
     *     )
     * )
     */
    public function store(TraderRegisterRequest $request)
    {
        $trader = $this->traderRegistration->register($request->all());

        return $this->responder->item($trader, new UnverifiedUserTransformer())->get();
    }

    public function show(UserRepository $userRepository,  $user)
    {
        if (!$user || ($user && !$user->hasRoleSlug('trader'))) {
            return $this->respondNotFound(trans('user::messages.user not found'));
        }

        return $this->responder->item($user, new TraderTransformer())->get();
    }
}
