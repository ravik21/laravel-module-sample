<?php

namespace Modules\User\Events;

class UserApprovalHasBeenRejected
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
