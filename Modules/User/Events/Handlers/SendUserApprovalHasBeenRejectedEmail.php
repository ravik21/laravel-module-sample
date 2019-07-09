<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Events\UserApprovalHasBeenRejected;
use Modules\User\Emails\User\TraderApprovalRejectedEmail;
use Modules\User\Emails\User\ExpertApprovalRejectedEmail;

class SendUserApprovalHasBeenRejectedEmail
{
    /**
     * @var Mailer
     */
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserApprovalHasBeenRejected $event)
    {
        $user = $event->user;

        $this->mailer->to($user->email)
                     ->send($user->hasRoleSlug('expert') ? new ExpertApprovalRejectedEmail($user) : new TraderApprovalRejectedEmail($user));
    }
}
