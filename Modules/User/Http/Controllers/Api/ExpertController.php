<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Http\Requests\ExpertRegisterRequest;
use Modules\User\Http\Requests\MarkAsFavouriteRequest;
use Modules\User\Http\Requests\FavouriteRequest;
use Modules\User\Http\Requests\ExpertRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;

use Modules\User\Repositories\UserRepository;
use Modules\User\Repositories\FavouriteRepository;
use Modules\User\Repositories\UserTokenRepository;
use Modules\User\Repositories\UserInviteRepository;

use Modules\User\Services\Expert\ExpertRegistration;

use Modules\User\Api\Transformers\UnverifiedUserTransformer;
use Modules\User\Api\Transformers\ExpertTransformer;

class ExpertController extends BaseApiController
{
    /**
     * @var ExpertRegistration
     */
    protected $expertRegistration;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserInviteRepository
     */
    protected $userInvite;

    /**
    * @var FavouriteRepository
    */
    protected $favouriteRepository;

    /**
     * Constructor
     *
     * @param ExpertRegistration $expertRegistration
     * @param UserInviteRepository $userInvite
     * @param FavouriteRepository $favouriteRepository
     */
    public function __construct(
      ExpertRegistration $expertRegistration,
      UserInviteRepository $userInvite,
      UserRepository $userRepository,
      FavouriteRepository $favouriteRepository)
    {
        $this->expertRegistration   = $expertRegistration;
        $this->userInvite           = $userInvite;
        $this->userRepository       = $userRepository;
        $this->favouriteRepository  = $favouriteRepository;

        parent::__construct();
    }

    public function index(ExpertRequest $request)
    {
        if($request->favourites == 'true') {
          $user = $this->userRepository->find($request->user);
          $favourites = $user->favourites()->pluck('favourited_user_id')->toArray();
          $request->offsetSet('favourites', $favourites);
        }

        $meta  = $request->only('search', 'terms', 'status', 'suspended', 'order_by', 'order', 'per_page', 'page', 'country', 'group', 'tags', 'favourites');
        $users = $this->userRepository->filterAndPaginateUsing($meta, ['expert']);

        return $this->responder->parseIncludes(request()->get('includes', []))
                               ->collection($users->getCollection(), new ExpertTransformer())
                               ->withPaginator($users)->get();
    }

    public function show(UserRepository $userRepository,  $user)
    {
        if (!$user || ($user && !$user->hasRoleSlug('expert'))) {
            return $this->respondNotFound(trans('user::messages.user not found'));
        }

        return $this->responder->item($user, new ExpertTransformer())->get();
    }
    /**
     * @SWG\Post(
     *     path="/experts/register",
     *     summary="Register a Expert.",
     *     tags={"Expert"},
     *     description="Permits a Registration attempt.",
     *     operationId="expertRegister",
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
     *                   property="password_confirmation",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="timezone",
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
     *             @SWG\Property(
     *                   property="tag_ids",
     *                   type="array",
     *                   @SWG\Items(
     *                      type="integer"
     *                   )
     *             ),
     *             @SWG\Property(
     *                   property="has_company",
     *                   type="boolean",
     *             ),
     *             @SWG\Property(
     *                   property="company_name",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="company_position",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="company_number",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="company_town",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="company_region",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="company_postcode",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="company_vat_registered",
     *                   type="boolean",
     *             ),
     *             @SWG\Property(
     *                   property="company_vat_no",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="hour_rate",
     *                   type="decimal",
     *             ),
     *             @SWG\Property(
     *                   property="day_rate",
     *                   type="decimal",
     *             ),
     *             @SWG\Property(
     *                   property="languages",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="availability",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="education",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="memberships",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="professional_experience",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="references",
     *                   type="string",
     *             ),
     *             @SWG\Property(
     *                   property="marketing",
     *                   type="boolean",
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
     *         description="Invalid ApiKey supplied.",
     *     ),
     *      @SWG\Response(
     *         response="403",
     *         description="Invalid invite code.",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.",
     *     )
     * )
     */
    public function store(ExpertRegisterRequest $request)
    {
        if (!$this->userInvite->isValidCode($request->get('invite_code', null))) {
             return $this->setStatusCode(403)->respondWithError('Invalid invite code');
        }

        if (!$this->userInvite->isValidEmail($request->all())) {
          return $this->setStatusCode(403)->respondWithError('Invalid Email');
        }

        $expert = $this->expertRegistration->register($request->all());


        return $this->responder->item($expert, new UnverifiedUserTransformer())->get();
    }

    public function markAsFavourite(MarkAsFavouriteRequest $request)
    {
        $data = [];

        foreach ($request->experts as $key => $expert) {
          $data = [
              'user_id' => $request->trader,
              'favourited_user_id'  => $expert
            ];

          $this->favouriteRepository->createOrUpdate($data);
        }

        return response()->json(['message' => 'success'],200);
    }

    public function unMarkAsFavourite(MarkAsFavouriteRequest $request)
    {
        $data = [];

        $favourites = $this->favouriteRepository->deleteFavourited($request->trader, $request->experts);

        return response()->json(['message' => 'success'],200);
    }

    public function markedAsFavourite(FavouriteRequest $request)
    {
        $trader      = $this->userRepository->find($request->trader);
        $favourites  = $trader->favourites()->get()->pluck('favourited_user_id');

        return response()->json(['favourites' => json_encode($favourites)],200);
    }

}
