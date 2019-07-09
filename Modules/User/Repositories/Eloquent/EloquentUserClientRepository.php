<?php

namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Modules\User\Events\UserLoggedInToClient;
use Modules\User\Repositories\UserClientRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentUserClientRepository extends EloquentBaseRepository implements UserClientRepository
{
    /**
     * Find or Create a Client for a User
     *
     * @param int $clientId
     * @param int $userId
     * @return UserClient $client
     */
    public function findOrCreateForUser($clientId, $userId)
    {
        $client = $this->model->where('client_id', $clientId)->where('user_id', $userId)->first();

        if (!$client) {
            $client = $this->model->create([
                'client_id' => $clientId,
                'user_id' => $userId,
            ]);
        }

        return $client;
    }

    public function loggedIn($clientId, $user)
    {
        $client = $this->findOrCreateForUser($clientId, $user->id);

        if ($client) {
            $client->login_count++;
            $client->last_login_at = \Carbon\Carbon::now();

            $client->save();

            event($event = new UserLoggedInToClient($client, $user));
        }
    }
}