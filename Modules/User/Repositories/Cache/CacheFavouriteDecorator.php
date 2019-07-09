<?php

namespace Modules\User\Repositories\Cache;

use Modules\User\Repositories\FavouriteRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheFavouriteDecorator extends BaseCacheDecorator implements FavouriteRepository
{
    /**
     * @var FavouriteRepository
     */
    protected $repository;

    public function __construct(FavouriteRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'favourites';
        $this->repository = $repository;
    }
}
