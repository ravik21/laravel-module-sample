<?php namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Repositories\UserRepository;
use Modules\User\Repositories\UserFileRepository;

use Modules\User\Http\Requests\UpdateProfileRequest;
use Modules\User\Http\Requests\UploadTermsConditionsRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\Avatarable\Http\Requests\UpdateAvatarRequest;

use Modules\User\Api\Transformers\UserTransformer;
use Modules\User\Api\Transformers\TraderTransformer;
use Modules\User\Api\Transformers\ExpertTransformer;
use Modules\User\Api\Transformers\FileTransformer;
use Modules\User\Enums\UserFileTypeEnum;

use Modules\User\Services\FileService;
use Modules\User\Events\FileWasUploaded;

class ProfileController extends BaseApiController
{
    /**
     * @var FileService
     */
    private $fileService;

    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var UserFileRepository
     */
    private $userFile;

    /**
     * Constructor
     *
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo, FileService $fileService, UserFileRepository $userFile)
    {
        $this->userRepo     = $userRepo;
        $this->fileService  = $fileService;
        $this->userFile     = $userFile;

        parent::__construct();
    }

     /**
     * @SWG\Get(
     *     path="/users/profile",
     *     summary="Get User Profile.",
     *     tags={"User"},
     *     description="Allow User to retrieve their profile.",
     *     operationId="userProfile",
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
     *         description="Please specify an ApiKey.",
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Invalid ApiKey supplied.",
     *     )
     * )
     */
    public function show()
    {
        return $this->responder->item($this->user, new UserTransformer($this->user->getLastApiToken()))->get();
    }

    /**
     * @SWG\Post(
     *     path="/users/profile",
     *     summary="Update a User Profile.",
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
     *         description="Update Profile Details for User.",
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
     *                   property="gender",
     *                   type="integer",
     *             ),
     *             @SWG\Property(
     *                   property="company_location_id",
     *                   type="integer",
     *             ),
     *             @SWG\Property(
     *                   property="company_team_id",
     *                   type="integer",
     *             ),
     *             @SWG\Property(
     *                   property="password",
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
    public function update(UpdateProfileRequest $request)
    {
        $profileDetails = $request->only('first_name', 'last_name', 'gender', 'password', 'company_location_id', 'company_team_id','title','company_name','company_number','parent_company_name','company_vat_no','company_phone_contact','company_position','company_street','company_town','company_region','company_postcode','group_ids','tag_ids','company_country_id','timezone','hour_rate','day_rate','languages','availability','professional_experience','past_projects','education','memberships','references', 'is_subscribed');

        $user       = $this->userRepo->update($this->user, $profileDetails);
        $apiToken   = $user->getLastApiToken();

        if ($user->hasRoleSlug('trader')) {
            return $this->responder->item($user, new TraderTransformer($apiToken))->get();
        }

        if($user->hasRoleSlug('expert')) {
            return $this->responder->item($user, new ExpertTransformer($apiToken))->get();
        }

        return $this->responder->item($user, new UserTransformer($apiToken))->get();
    }

    /**
     * @SWG\Post(
     *     path="/users/profile/avatar",
     *     consumes={"multipart/form-data"},
     *     summary="Upload Avatar.",
     *     tags={"User"},
     *     description="Allows logged in User to upload a valid image for their Avatar.",
     *     operationId="userAvatarUpdate",
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
     *         name="avatar",
     *         description="Avatar File to upload.",
     *         in="formData",
     *         required=true,
     *         type="file"
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
    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $avatar = $this->user->saveAvatar($this->user->present()->fullname, $request->file('avatar'));

        return response()->json([
            'avatar' => $this->user->getAvatarUrl(),
            'message' => trans('user::messages.user avatar uploaded'),
        ]);
    }

    public function uploadTermsConditions(UploadTermsConditionsRequest $request)
    {
        $savedFile = $this->fileService->store($request->file('file'), $request->get('parent_id'));

        if (is_string($savedFile)) {
          return response()->json([
            'error' => $savedFile,
          ], 409);
        }

        $userFile = $this->userFile->createOrUpdate([
          'file_id' => $savedFile->id,
          'user_id' => $this->user->id,
          'type'    => UserFileTypeEnum::TERMS_CONDITIONS
        ]);

        event(new FileWasUploaded($savedFile));

        return $this->responder->item($savedFile, new FileTransformer())->get();
    }

    public function deleteTermsConditions(Request $request)
    {
      $file = $this->fileService->destroy($request->id);
      return response()->json([
          'message' => trans('user::messages.file deleted')
      ]);
    }
}
