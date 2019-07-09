<?php

namespace Modules\User\Repositories\Cache;

use Modules\User\Repositories\UserFileRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheUserFileDecorator extends BaseCacheDecorator implements UserFileRepository
{
    /**
     * @var UserFileRepository
     */
    protected $repository;

    public function __construct(UserFileRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'user_files';
        $this->repository = $repository;
    }
}
