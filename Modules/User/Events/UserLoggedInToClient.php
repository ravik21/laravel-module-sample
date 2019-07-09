<?php

namespace Modules\User\Events;

class UserLoggedInToClient
{
    public $client;
    public $user;

    public function __construct($client, $user)
    {
        $this->client = $client;
        $this->user = $user;
    }
}
