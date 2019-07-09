<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Modules\Setting\Contracts\Setting;
use Modules\User\Entities\UserInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var UserInterface
     */
    public $user;

    /**
     * @var
     */
    public $activationCode;

    /**
     * @var
     */
    public $sitename;

    public function __construct(UserInterface $user, $activationCode)
    {
        $this->user = $user;
        $this->activationCode = $activationCode;

        $this->sitename = app(Setting::class)->get('core::site-name', App::getLocale());
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), trans('user::email.subject.alegrant-registration'))
                    ->view('user::emails.verify')
                    ->subject(trans('user::email.subject.user registered', ['sitename' => $this->sitename]));
    }
}
