<?php

namespace Modules\User\Events;

class UserApprovalHasBeenAccepted
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
