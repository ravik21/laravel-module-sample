<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Events\NotifyExpertForMessage;
use Modules\User\Emails\User\TraderContactedExpertEmail;

class SendChatMessageEmail
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(NotifyExpertForMessage $event)
    {
        $trader  = $event->trader;
        $expert  = $event->expert;

        $this->mailer->to($expert->email)->send(new TraderContactedExpertEmail($trader, $expert));
    }
}
