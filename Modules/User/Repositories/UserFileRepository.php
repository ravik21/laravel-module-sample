<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface UserFileRepository extends BaseRepository
{
    /**
     * Create the userFiles
     * @param Array $data
     * @return mixed|void
     */
    public function create($data);

    public function createOrUpdate($data);

}
