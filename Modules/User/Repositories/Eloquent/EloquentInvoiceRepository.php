<?php

namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Repositories\InvoiceRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentInvoiceRepository extends EloquentBaseRepository implements InvoiceRepository
{
    public function create($data)
    {
        $invoice = parent::create($data);
        return $invoice;
    }
    public function filterAndPaginateUsing($meta) : LengthAwarePaginator
    {
        $invoice = $this->model->query();
        if ($this->hasOrderByMeta($meta)) {
            $invoice->orderBy($meta['order_by'], ($meta['order'] === 'asc' || $meta['order'] === 'ascending') ? 'asc' : 'desc');
        } else {
            $invoice->orderBy('id', 'desc');
        }
        return  $invoice->select('invoices.*')->paginate($this->hasMeta('per_page', $meta) ? $meta['per_page'] : 15)
                        ->appends($_GET);
    }
}
