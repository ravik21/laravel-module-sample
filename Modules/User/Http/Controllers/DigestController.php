<?php

namespace Modules\User\Http\Controllers;

use Carbon\Carbon;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\User\Repositories\UserRepository;

class DigestController extends BasePublicController
{
    /**
     * @var UserRepository
     */
    private $user;

    public function __construct(UserRepository $user)
    {
        $this->user       = $user;

        parent::__construct();
    }

    public function unsubscribe($userId)
    {
        $user       = $this->user->find($userId);
        $user->digest_unsubscribed_at = Carbon::now();
        $user->save();

        return redirect()->route('login')
            ->withSuccess(trans('user::messages.unsubscribed to digest'));
    }
}
