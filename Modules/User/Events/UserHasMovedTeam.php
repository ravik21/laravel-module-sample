<?php

namespace Modules\User\Events;

class UserHasMovedTeam
{
    public $user;

    public $team;

    public function __construct($user, $team)
    {
        $this->user = $user;
        $this->team = $team;
    }
}
