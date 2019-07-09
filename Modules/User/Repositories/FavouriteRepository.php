<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface FavouriteRepository extends BaseRepository
{
    /**
     * Create the favourites
     * @param Array $favourites
     * @return mixed|void
     */
    public function create($favourite);

    /**
     * Create or update the user_id and user_favourited_id
     * @param $favourite
     * @return mixed|void
     */
    public function createOrUpdate($favourite);

    public function findByUserFavourite($data);

    public function deleteFavourited($userId, $experts);

}
