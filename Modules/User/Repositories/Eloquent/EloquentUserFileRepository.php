<?php

namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Repositories\UserFileRepository;

class EloquentUserFileRepository extends EloquentBaseRepository implements UserFileRepository
{
    public function create($data)
    {
        return parent::create($data);
    }

    public function createOrUpdate($data)
    {
        $userFile = $this->model->where('user_id', $data['user_id'])
                                ->where('type', $data['type'])
                                ->first();

        if ($userFile) {
            $this->update($userFile, $data);
        } else {
            $this->create($data);
        }
    }

}
