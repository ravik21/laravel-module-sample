<?php

namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Routing\Controller;
use Modules\User\Permissions\PermissionManager;
use Modules\Core\Http\Controllers\Api\BaseApiController;

class PermissionController extends BaseApiController
{
    /**
     * @var PermissionManager
     */
    protected $permissionManager;

    public function __construct(PermissionManager $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    public function index()
    {
        return response()->json([
            'permissions' => $this->permissionManager->all(),
        ]);
    }
}