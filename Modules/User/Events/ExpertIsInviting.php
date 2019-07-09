<?php

namespace Modules\User\Events;

class ExpertIsInviting
{
    public $invite;
    public $inviter;

    public function __construct($invite, $inviter)
    {
        $this->invite = $invite;
        $this->inviter = $inviter;
    }
}
