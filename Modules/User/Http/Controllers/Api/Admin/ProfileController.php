<?php

namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Routing\Controller;
use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserRepository;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Api\Transformers\Admin\UserTransformer;
use Modules\Avatarable\Http\Requests\UpdateAvatarRequest;
use Modules\User\Http\Requests\Admin\UpdateProfileRequest;

class ProfileController extends BaseApiController
{
    /**
     * @var Authentication
     */
    private $auth;
    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(Authentication $auth, UserRepository $userRepo)
    {
        $this->auth     = $auth;
        $this->userRepo = $userRepo;

        parent::__construct();
    }

    public function show()
    {
        return $this->responder->item($this->user, new UserTransformer())->get();
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->userRepo->update($this->user, $request->only('first_name', 'last_name', 'email', 'password'));

        return response()->json([
            'message' => trans('user::messages.profile updated'),
        ]);
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $avatar = $this->user->saveAvatar($this->user->present()->fullname, $request->file('avatar'));

        return response()->json([
            'avatar' => $this->user->getAvatarUrl(),
            'message' => trans('user::messages.user avatar uploaded'),
        ]);
    }
}