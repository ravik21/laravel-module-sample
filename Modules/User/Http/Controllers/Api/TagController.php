<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Api\Transformers\TagTransformer;
use Modules\User\Http\Requests\UpdateTagsRequest;
use Modules\User\Repositories\UserRepository;

class TagController extends BaseApiController
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;

        parent::__construct();
    }

    /**
     * @SWG\Get(
     *     path="/users/tags",
     *     summary="Returns list of User tags",
     *     tags={"User"},
     *     description="Returns list of Tag items that is available for the logged in User.",
     *     operationId="listUserTags",
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
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Please specify a ApiKey or Authorization (User Token).",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Invalid ApiKey or Authorization (User Token).",
     *     )
     * )
     */
    public function index(Request $request)
    {
        $tags = $this->user->tags()->join('tag__tag_translations', 'tag__tag_translations.tag_id', '=', 'tag__tags.id')
                     ->select('tag__tags.*')
                     ->where('locale', 'en')
                     ->orderBy('name');

        if ($request->has('enabled')) {
            $tags = $tags->where('enabled', true);
        }

        return $this->responder->collection($tags->get(), new TagTransformer())->get();
    }

    /**
     * @SWG\Post(
     *     path="/users/tags",
     *     summary="Update User's Tags.",
     *     tags={"User"},
     *     description="Allow User to update their profile.",
     *     operationId="userUpdate",
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
     *         description="Update Tags for User.",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                   property="tags",
     *                   type="array",
     *                   @SWG\Items(
     *                       type="object",
     *                       @SWG\Property(
     *                           property="id",
     *                           type="integer",
     *                       ),
     *                       @SWG\Property(
     *                           property="enabled",
     *                           type="boolean",
     *                       )
     *                  )
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
    public function store(UpdateTagsRequest $request)
    {
        if($request->id) {
          $user = $this->userRepo->find($request->id);
        } else {
          $user = $this->user;
        }
        $user = $this->userRepo->updateTags($user, $request->only('tags'));

        return response()->json(["message" => "Tags updated successfully"], 200);
    }
}
