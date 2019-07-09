<?php

namespace Modules\User\Emails\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Modules\Setting\Contracts\Setting;
use Illuminate\Queue\SerializesModels;
use Modules\User\Entities\UserInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class DigestEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var email
     */
    public $email;

    /**
     * @var mixed Data
     */
    public $data;

    /**
     * @var
     */
    public $sitename;

    public function __construct($email, $data)
    {
        $this->email = $email;
        $this->data = $data;

        $this->sitename = app(Setting::class)->get('core::site-name', App::getLocale());
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('user::emails.users.digest', $this->data)
            ->subject(trans('user::email.subject.monthly digest', ['month' => $this->data['month']->format('F')]));
    }
}
