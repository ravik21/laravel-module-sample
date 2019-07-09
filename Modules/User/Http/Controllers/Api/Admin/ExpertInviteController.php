<?php

namespace Modules\User\Http\Controllers\Api\Admin;

use Modules\User\Events\ExpertIsInviting;
use Modules\User\Repositories\UserInviteRepository;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Http\Requests\Admin\SendExpertInviteRequest;

class ExpertInviteController extends BaseApiController
{
    /**
     * @var UserInviteRepository
     */
    private $userInvite;

    /**
     * Constructor
     *
     * @param UserInviteRepository $userInvite
     */
    public function __construct(UserInviteRepository $userInvite)
    {
        $this->userInvite = $userInvite;

        parent::__construct();
    }

    public function store(SendExpertInviteRequest $request)
    {
        foreach ($request->get('recipients') as $email) {
            $invite = $this->userInvite->createOrUpdateForEmail($this->user, $email, $request->get('message'));

            if ($invite) {
                event(new ExpertIsInviting($invite, $this->user));
            }
        }

         return $this->respond([
            'message' => trans('user::messages.expert invite email was sent'),
        ]);
    }
}
