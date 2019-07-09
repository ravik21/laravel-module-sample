<?php

namespace Modules\User\Emails\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Modules\Setting\Contracts\Setting;
use Illuminate\Queue\SerializesModels;
use Modules\User\Entities\UserInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteEmail extends Mailable implements ShouldQueue
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
    public $role;

    /**
     * @var
     */
    public $sitename;

    public function __construct(UserInterface $user, $activationCode)
    {
        $this->user = $user;
        $this->activationCode = $activationCode;

        $role = $this->user->roles->first();
        $this->role = $role ? $role->name : '';

        $this->sitename = app(Setting::class)->get('core::site-name', App::getLocale());
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('user::emails.admins.invite')
                    ->subject(trans('user::email.subject.admin invited', ['sitename' => $this->sitename]));
    }
}
