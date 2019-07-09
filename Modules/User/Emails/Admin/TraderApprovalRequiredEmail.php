<?php

namespace Modules\User\Emails\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TraderApprovalRequiredEmail extends Mailable implements ShouldQueue
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
        return $this->from(env('MAIL_FROM_ADDRESS'), trans('user::email.subject.alegrant-registration'))
                    ->view('user::emails.admins.trader-approval-required')
                    ->subject(trans('user::email.subject.trader-approval-required'));
    }
}
