<?php

namespace Modules\User\Emails\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TraderApprovalAccepetedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var user
     */
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('user::emails.users.trader-approval-accepted')
                    ->subject(trans('user::email.subject.trader-approval-accepted'));
    }
}
