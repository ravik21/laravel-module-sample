<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface ChatMessageRepository extends BaseRepository
{
    /**
     * Create the ChatMessage
     * @param Array $chatMessage
     * @return mixed|void
     */
    public function create($chatMessage);
}
