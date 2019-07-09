<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Events\UserApprovalHasBeenAccepted;
use Modules\User\Emails\User\TraderApprovalAccepetedEmail;
use Modules\User\Emails\User\ExpertApprovalAccepetedEmail;

class SendUserApprovalHasBeenAcceptedEmail
{
    /**
     * @var Mailer
     */
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserApprovalHasBeenAccepted $event)
    {
        $user = $event->user;

        $this->mailer->to($user->email)
                     ->send($user->hasRoleSlug('expert') ? new ExpertApprovalAccepetedEmail($user) : new TraderApprovalAccepetedEmail($user));
    }
}
