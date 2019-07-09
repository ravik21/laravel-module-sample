<?php

namespace Modules\User\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Contracts\Authentication;
use Modules\User\Emails\VerifyEmail;
use Modules\User\Emails\Admin\InviteEmail;
use Modules\User\Emails\Admin\WelcomeEmail;

class SendRegistrationConfirmationEmail
{
    /**
     * @var AuthenticationRepository
     */
    private $auth;
    /**
     * @var Mailer
     */
    private $mail;

    public function __construct(Authentication $auth, Mailer $mail)
    {
        $this->auth = $auth;
        $this->mail = $mail;
    }

    public function handle($event)
    {
        $user      = $event->user;
        $password  = $event->password;
        $isInvite  = $event->isInvite;

        $activationCode = $this->auth->createActivation($user, $isInvite);

        if ($user->hasRoleSlug('trader') || $user->hasRoleSlug('expert')) {
            $this->mail->to($user->email)->send(new VerifyEmail($user, $activationCode));
        } else if ($event->isInvite) {
            $this->mail->to($user->email)->send(new InviteEmail($user, $activationCode));
        } else {
            $this->mail->to($user->email)->send(new WelcomeEmail($user, $password, $activationCode));
        }
    }
}
