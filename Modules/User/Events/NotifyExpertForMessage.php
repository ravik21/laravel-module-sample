<?php

namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class NotifyExpertForMessage
{
    public $trader;
    public $expert;

    public function __construct($trader, $expert)
    {
        $this->trader = $trader;
        $this->expert = $expert;
    }
}
