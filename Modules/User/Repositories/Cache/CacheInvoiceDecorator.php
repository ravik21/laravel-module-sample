<?php

namespace Modules\User\Repositories\Cache;

use Modules\User\Repositories\InvoiceRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheInvoiceDecorator extends BaseCacheDecorator implements InvoiceRepository
{
    /**
     * @var InvoiceRepository
     */
    protected $repository;

    public function __construct(InvoiceRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'invoices';
        $this->repository = $repository;
    }
}
