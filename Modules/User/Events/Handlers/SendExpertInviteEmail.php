<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Events\ExpertIsInviting;
use Modules\User\Events\ExpertWasInvited;
use Modules\User\Emails\Admin\ExpertInviteEmail;

class SendExpertInviteEmail
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(ExpertIsInviting $event)
    {
        $invite  = $event->invite;
        $inviter = $event->inviter;

        $this->mailer->to($invite->email)->send(new ExpertInviteEmail($invite->email, $invite->code, $invite->message, $inviter, $invite));

        event(new ExpertWasInvited($invite, $inviter));
    }
}
