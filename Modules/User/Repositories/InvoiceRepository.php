<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface InvoiceRepository extends BaseRepository
{
    /**
     * Create the invoice
     * @param Array $invoice
     * @return mixed|void
     */
    public function create($invoice);
}
