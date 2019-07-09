<?php

namespace Modules\User\Repositories\Cache;

use Modules\User\Repositories\ChatMessageRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheChatMessageDecorator extends BaseCacheDecorator implements ChatMessageRepository
{
    /**
     * @var ChatMessageRepository
     */
    protected $repository;

    public function __construct(ChatMessageRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'chat_messages';
        $this->repository = $repository;
    }
}
