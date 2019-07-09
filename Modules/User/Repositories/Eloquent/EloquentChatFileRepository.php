<?php

namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Repositories\ChatFileRepository;

class EloquentChatFileRepository extends EloquentBaseRepository implements ChatFileRepository
{
    public function create($data)
    {
        return parent::create($data);
    }
}
