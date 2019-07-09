<?php

namespace Modules\User\Emails\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpertApprovalRequiredEmail extends Mailable implements ShouldQueue
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
                    ->view('user::emails.admins.expert-approval-required')
                    ->subject(trans('user::email.subject.expert-approval-required'));
    }
}
