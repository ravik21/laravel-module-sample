<?php

namespace Modules\User\Events;

class UserHasRegistered
{
    public $user;
    public $password;
    public $isInvite;

    public function __construct($user, $password, $isInvite = false)
    {
        $this->user = $user;
        $this->password = $password;
        $this->isInvite = $isInvite;
    }
}
