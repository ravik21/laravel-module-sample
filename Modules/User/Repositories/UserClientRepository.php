<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface UserClientRepository extends BaseRepository
{
    /**
     * Find or Create a Client for a User
     *
     * @param int $clientId
     * @param int $userId
     * @return UserClient $client
     */
    public function findOrCreateForUser($clientId, $userId);
}
