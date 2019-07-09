<?php

namespace Modules\User\Emails\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\User\Entities\UserInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpertInviteEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var email
     */
    public $email;

    /**
     * @var code
     */
    public $code;

    /**
     * @var customMessage
     */
    public $customMessage;

    /**
     * @var User
     */
    public $inviter;

    public $invite;

    /**
     * @var url
     */
    public $url;

    public function __construct($email, $code, $message, $inviter, $invite)
    {
        $this->email = $email;
        $this->code = $code;
        $this->customMessage = $message;
        $this->inviter = $inviter;
        $this->url = env('EXPERT_WEB_SIGNUP_URL') . '?code=' . $code . '&email=' . $email;
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('user::emails.admins.expert-invite')
                    ->subject(trans('user::email.subject.expert invited'));
    }
}
