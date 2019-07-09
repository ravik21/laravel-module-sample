<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface ChatFileRepository extends BaseRepository
{
    /**
     * Create the chatFiles
     * @param Array $chatFile
     * @return mixed|void
     */
    public function create($chatFile);
}
