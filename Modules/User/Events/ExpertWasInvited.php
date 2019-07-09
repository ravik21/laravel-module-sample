<?php

namespace Modules\User\Events;

class ExpertWasInvited
{
    public $invite;
    public $inviter;

    public function __construct($invite, $inviter)
    {
        $this->invite = $invite;
        $this->inviter = $inviter;
    }
}
