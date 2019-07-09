<?php

namespace Modules\User\Http\Controllers\Admin;

class InvoiceController extends BaseUserModuleController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Vue it!
     *
     * @return Response
     */
    public function vue()
    {
        return view('user::admin.vue');
    }
}
