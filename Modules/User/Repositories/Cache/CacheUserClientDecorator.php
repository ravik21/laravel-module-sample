<?php

namespace Modules\User\Repositories\Cache;

use Modules\User\Repositories\UserClientRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheUserClientDecorator extends BaseCacheDecorator implements UserClientRepository
{
    /**
     * @var UserTokenRepository
     */
    protected $repository;

    public function __construct(UserClientRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'user-clients';
        $this->repository = $repository;
    }

    /**
     * Find or Create a Client for a User
     *
     * @param int $clientId
     * @param int $userId
     * @return UserClient $client
     */
    public function findOrCreateForUser($clientId, $userId)
    {
        $this->remember(function () use ($clientId, $userId) {
            return $this->repository->findOrCreateForUser($clientId, $userId);
        });
    }
}
