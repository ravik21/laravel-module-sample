<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Events\UserHasRegistered;
use Modules\User\Repositories\UserRepository;
use Modules\User\Emails\Admin\TraderApprovalRequiredEmail;
use Modules\User\Emails\Admin\ExpertApprovalRequiredEmail;

class SendUserApprovalRequiredEmail
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var UserRepository
     */
    protected $user;

    public function __construct(Mailer $mailer, UserRepository $user)
    {
        $this->mailer = $mailer;
        $this->user = $user;
    }

    public function handle(UserHasRegistered $event)
    {
        $user = $event->user;
        $admins = $this->user->queryByRoleSlugs(['super-admin', 'admin'])->get();

        foreach ($admins as $admin) {
            $this->mailer->to($admin->email)->send($user->hasRoleSlug('expert') ? new ExpertApprovalRequiredEmail($user) : new TraderApprovalRequiredEmail($user));
        }
    }
}
