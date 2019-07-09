<?php

namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Group\Entities\Group;
use Modules\Group\Repositories\GroupRepository;
use Modules\Group\Http\Requests\Admin\CreateGroupRequest;
use Modules\Group\Http\Requests\Admin\UpdateGroupRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\Group\Api\Transformers\Admin\GroupTransformer;

class GroupController extends BaseApiController
{
    /**
     * @var GroupRepository
     */
    protected $group;

    public function __construct(GroupRepository $group)
    {
        parent::__construct();

        $this->group = $group;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $meta = $request->only('search', 'order_by', 'order', 'per_page', 'page');

        $groups = $this->group->filterAndPaginateUsing($meta);

        return $this->responder->parseIncludes(request()->get('includes', []))->collection($groups->getCollection(), new GroupTransformer())->withPaginator($groups)->get();
    }
}
