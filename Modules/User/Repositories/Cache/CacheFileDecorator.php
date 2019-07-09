<?php

namespace Modules\User\Repositories\Cache;

use Modules\User\Repositories\FileRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheFileDecorator extends BaseCacheDecorator implements FileRepository
{
    /**
     * @var FileRepository
     */
    protected $repository;

    public function __construct(FileRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'files';
        $this->repository = $repository;
    }
}
