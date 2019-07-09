<?php

namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Repositories\InvoiceRepository;
use Modules\User\Api\Transformers\InvoiceTransformer;
use Modules\Core\Http\Controllers\Api\BaseApiController;

class InvoiceController extends BaseApiController
{
    /**
    * @var InvoiceRepository
    */
    private $invoiceRepo;

    public function __construct(InvoiceRepository $invoiceRepo)
    {
          $this->invoiceRepo = $invoiceRepo;
          parent::__construct();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
     public function index(Request $request)
     {
          $meta  = $request->only('order_by', 'order', 'per_page', 'page');
          $invoices = $this->invoiceRepo->filterAndPaginateUsing($meta, ['user']);

          return $this->responder->collection($invoices->getCollection(), new InvoiceTransformer())
                                 ->withPaginator($invoices)
                                 ->get();
     }
}
