<?php

namespace Modules\User\Events;

class TraderIsInviting
{
    public $email;
    public $inviter;

    public function __construct($email, $inviter)
    {
        $this->email = $email;
        $this->inviter = $inviter;
    }
}
