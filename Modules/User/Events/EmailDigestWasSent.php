<?php

namespace Modules\User\Events;

class EmailDigestWasSent
{
    public $user;

    public function __construct($user)
    {
        $user->update(['digest_sent' => 1]);
        $this->user = $user;
    }
}
