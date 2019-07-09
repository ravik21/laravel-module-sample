<?php

namespace Modules\User\Emails\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\User\Entities\UserInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class TraderInviteEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var email
     */
    public $email;

    /**
     * @var User
     */
    public $inviter;

    public function __construct($email, $inviter)
    {
        $this->email = $email;
        $this->inviter = $inviter;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('user::emails.users.trader-invite')
                    ->subject(trans('user::email.subject.trader invited', [
                        'inviter' => $this->inviter->present()->fullname()
                    ]));
    }
}
