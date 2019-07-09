<?php

namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Taggable\Entities\Tag;
use Modules\Taggable\Repositories\TagRepository;
use Modules\Taggable\Http\Requests\Admin\CreateTagRequest;
use Modules\Taggable\Http\Requests\Admin\UpdateTagRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\Taggable\Api\Transformers\Admin\TagTransformer;

class TagController extends BaseApiController
{
    /**
     * @var TagRepository
     */
    protected $tag;

    public function __construct(TagRepository $tag)
    {
        parent::__construct();

        $this->tag = $tag;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $meta = $request->only('search', 'order_by', 'order', 'per_page', 'page');

        $tags = $this->tag->filterAndPaginateUsing($meta);

        return $this->responder->parseIncludes(request()->get('includes', []))->collection($tags->getCollection(), new TagTransformer())->withPaginator($tags)->get();
    }
}
