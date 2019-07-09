<?php

namespace Modules\User\Events;

class AdminHasRegistered
{
    public $user;
    public $isInvite;

    public function __construct($user, $password, $isInvite = false)
    {
        $this->user = $user;
        $this->password = $password;
        $this->isInvite = $isInvite;
    }
}
