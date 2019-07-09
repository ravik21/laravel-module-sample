<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Events\TraderIsInviting;
use Modules\User\Events\TraderWasInvited;
use Modules\User\Emails\User\TraderInviteEmail;

class SendTraderInviteEmail
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(TraderIsInviting $event)
    {
        $email   = $event->email;
        $inviter = $event->inviter;

        $this->mailer->to($email)->send(new TraderInviteEmail($email, $inviter));

        event(new TraderWasInvited($email, $inviter));
    }
}
