<?php

namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\QueryException;
use Modules\User\Repositories\UserTokenRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentUserTokenRepository extends EloquentBaseRepository implements UserTokenRepository
{
    /**
     * Get all tokens for the given user
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allForUser($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * @param int $userId
     * @return \Modules\User\Entities\UserToken
     */
    public function generateFor($userId)
    {
        try {
            $uuid4     = Uuid::uuid4();
            $userToken = $this->model->create(['user_id' => $userId, 'access_token' => $uuid4, 'expires_at' => Carbon::now()->addWeeks(2)]);
        } catch (QueryException $e) {
            $this->generateFor($userId);
        }

        return $userToken;
    }

    /**
     * Determines if given User Token has expired.
     * @param string $userToken
     * @return bool
     */
    public function hasExpired($userToken)
    {
        return $this->model->unexpired()->where('access_token', $userToken)->count() == 0;
    }
}
