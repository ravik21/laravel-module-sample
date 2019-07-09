<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface UserTokenRepository extends BaseRepository
{
    /**
     * Get all tokens for the given user
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allForUser($userId);

    /**
     * @param int $userId
     * @return \Modules\User\Entities\UserToken
     */
    public function generateFor($userId);

    /**
     * Determines if given User Token has expired.
     * @param string $userToken
     * @return bool
     */
    public function hasExpired($userToken);
}
