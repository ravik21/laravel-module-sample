<?php

namespace Modules\User\Repositories\Cache;

use Modules\User\Repositories\UserInviteRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheUserInviteDecorator extends BaseCacheDecorator implements UserInviteRepository
{
    /**
     * @var UserInviteRepository
     */
    protected $repository;

    public function __construct(UserInviteRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'user-invites';
        $this->repository = $repository;
    }
}
