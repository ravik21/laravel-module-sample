<?php

namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Repositories\FavouriteRepository;

class EloquentFavouriteRepository extends EloquentBaseRepository implements FavouriteRepository
{
    public function create($data)
    {
        $favourite = parent::create($data);
        return $favourite;
    }

    public function createOrUpdate($data)
    {
        if ($favourite = $this->findByUserFavourite($data)) {
          $this->update($favourite, $data);
        } else
          $this->create($data);
    }

    public function findByUserFavourite($data)
    {
        return $this->model->where($data)->first();
    }

    public function deleteFavourited($userId, $experts)
    {
        $query = $this->model->query();
        return $query->whereIn('favourited_user_id', $experts)->where('user_id', $userId)->delete();

    }
}
