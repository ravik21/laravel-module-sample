<?php

namespace Modules\User\Events;

use Modules\User\Entities\ChatMessage;

class ChatMessageWasCreated
{
  public $entity;

  public function __construct($chatMessage)
  {
      $this->entity       = $chatMessage;
  }
}
